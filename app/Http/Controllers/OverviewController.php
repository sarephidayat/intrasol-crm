<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\CashTransaction;
use App\Models\Reconciliation;
use App\Services\CashBalanceService;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function index(Request $request, CashBalanceService $service)
    {
        $balance = $service->getCurrentBalance();
        $month = $service->getCurrentMonthTotals();
        $monthly = $service->getMonthlyTotals();
        $year = (int) now()->year;
        $expenseByCategory = $service->getExpenseByCategory($year);

        $recent = CashTransaction::with(['category', 'party'])
            ->active()
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $unpaidBills = Bill::whereNull('archived_at')
            ->where('status', '!=', 'paid')
            ->where('outstanding_amount', '>', 0);

        $receivables = (clone $unpaidBills)->where('type', 'receivable')->sum('outstanding_amount');
        $payables = (clone $unpaidBills)->where('type', 'payable')->sum('outstanding_amount');

        $upcomingBills = (clone $unpaidBills)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now()->addDays(7)->toDateString())
            ->with('party')
            ->orderBy('due_date')
            ->get();

        $overdueBills = $upcomingBills->filter(fn (Bill $bill) => $bill->due_date->lt(today()));
        $dueSoonBills = $upcomingBills->filter(
            fn (Bill $bill) => $bill->due_date->betweenIncluded(today(), today()->addDays(7))
        );

        $latestReconciliation = Reconciliation::latest('reconciliation_date')->first();

        return view('overview.index', compact(
            'balance',
            'month',
            'monthly',
            'year',
            'recent',
            'receivables',
            'payables',
            'overdueBills',
            'dueSoonBills',
            'latestReconciliation',
            'expenseByCategory',
        ));
    }
}
