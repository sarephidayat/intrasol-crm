<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\CashTransaction;
use App\Models\DirectorAdvance;
use App\Models\Reconciliation;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    public function periodTransactions(string $from, string $to): Collection
    {
        return CashTransaction::with(['category', 'party', 'project'])
            ->active()
            ->whereBetween('transaction_date', [$from, $to])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array{income: int, expense: int, net: int}
     */
    public function periodTotals(string $from, string $to): array
    {
        $totals = CashTransaction::active()
            ->whereBetween('transaction_date', [$from, $to])
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            ->first();

        $income = (int) ($totals->income ?? 0);
        $expense = (int) ($totals->expense ?? 0);

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
        ];
    }

    /**
     * @return array{receivables: int, payables: int, advances: int}
     */
    public function outstandingTotals(): array
    {
        return [
            'receivables' => (int) Bill::where('type', 'receivable')
                ->whereNull('archived_at')
                ->where('status', '!=', 'paid')
                ->sum('outstanding_amount'),
            'payables' => (int) Bill::where('type', 'payable')
                ->whereNull('archived_at')
                ->where('status', '!=', 'paid')
                ->sum('outstanding_amount'),
            'advances' => (int) DirectorAdvance::where('status', '!=', 'reimbursed')
                ->sum('outstanding_amount'),
        ];
    }

    public function bills(string $type): Collection
    {
        return Bill::with(['party', 'project'])
            ->where('type', $type)
            ->whereNull('archived_at')
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();
    }

    public function latestReconciliation(?int $id = null): ?Reconciliation
    {
        $query = Reconciliation::with(['bankStatement.entries', 'creator'])
            ->orderByDesc('reconciliation_date');

        if ($id) {
            $query->whereKey($id);
        }

        return $query->first();
    }
}
