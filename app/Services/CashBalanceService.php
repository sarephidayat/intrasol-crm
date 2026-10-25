<?php

namespace App\Services;

use App\Models\CashTransaction;
use Illuminate\Support\Facades\DB;

class CashBalanceService
{
    public function getCurrentBalance(): int
    {
        $income = CashTransaction::active()
            ->where('type', 'income')
            ->sum('amount');

        $expense = CashTransaction::active()
            ->where('type', 'expense')
            ->sum('amount');

        return (int) $income - (int) $expense;
    }

    public function getCurrentMonthTotals(): array
    {
        $start = now()->startOfMonth()->toDateString();
        $end = now()->endOfMonth()->toDateString();

        $income = CashTransaction::active()
            ->whereBetween('transaction_date', [$start, $end])
            ->where('type', 'income')
            ->sum('amount');

        $expense = CashTransaction::active()
            ->whereBetween('transaction_date', [$start, $end])
            ->where('type', 'expense')
            ->sum('amount');

        return [
            'income' => (int) $income,
            'expense' => (int) $expense,
            'net' => (int) $income - (int) $expense,
        ];
    }

    /**
     * Realized income/expense totals for each month of a calendar year.
     *
     * @return array<int, array{income: int, expense: int, net: int}>
     */
    public function getMonthlyTotals(?int $year = null): array
    {
        $year ??= (int) now()->year;

        $transactions = CashTransaction::active()
            ->whereBetween('transaction_date', ["{$year}-01-01", "{$year}-12-31"])
            ->get(['transaction_date', 'type', 'amount']);

        $totals = [];
        for ($month = 1; $month <= 12; $month++) {
            $totals[$month] = ['income' => 0, 'expense' => 0, 'net' => 0];
        }

        foreach ($transactions as $transaction) {
            $month = (int) $transaction->transaction_date->month;

            if ($transaction->type === 'income') {
                $totals[$month]['income'] += (int) $transaction->amount;
            } else {
                $totals[$month]['expense'] += (int) $transaction->amount;
            }
        }

        foreach ($totals as $month => $values) {
            $totals[$month]['net'] = $values['income'] - $values['expense'];
        }

        return $totals;
    }

    /**
     * Expense distribution by category for a calendar year (active, non-archived only).
     *
     * @return array{items: array<int, array{name: string, amount: int, percent: float}>, total: int}
     */
    public function getExpenseByCategory(?int $year = null): array
    {
        $year ??= (int) now()->year;

        $rows = CashTransaction::active()
            ->whereBetween('cash_transactions.transaction_date', ["{$year}-01-01", "{$year}-12-31"])
            ->where('cash_transactions.type', 'expense')
            ->leftJoin('categories', 'categories.id', '=', 'cash_transactions.category_id')
            ->selectRaw('COALESCE(categories.name, ?) as category_name, SUM(cash_transactions.amount) as total', ['Tanpa Kategori'])
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        $total = (int) $rows->sum(fn ($row) => (int) $row->total);

        $items = [];
        foreach ($rows as $row) {
            $amount = (int) $row->total;
            $percent = $total > 0 ? round($amount / $total * 100, 1) : 0.0;
            $items[] = [
                'name' => $row->category_name,
                'amount' => $amount,
                'percent' => $percent,
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }
}
