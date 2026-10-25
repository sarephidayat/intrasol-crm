<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\BillPaymentController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DirectorAdvanceController;
use App\Http\Controllers\DirectorReimbursementController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/overview', [OverviewController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('overview');

Route::get('/dashboard', [OverviewController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('parties', PartyController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('transactions', CashTransactionController::class)->except(['destroy']);
    Route::post('transactions/{transaction}/archive', [CashTransactionController::class, 'archive'])->name('transactions.archive');
    Route::get('receipts/{transaction}/view', [ReceiptController::class, 'show'])->name('receipts.view');
    Route::get('receipts/{transaction}/download', [ReceiptController::class, 'download'])->name('receipts.download');

    Route::resource('bills', BillController::class);
    Route::post('bills/{bill}/archive', [BillController::class, 'archive'])->name('bills.archive');
    Route::get('bills/{bill}/pay', [BillPaymentController::class, 'create'])->name('bills.pay.create');
    Route::post('bills/{bill}/pay', [BillPaymentController::class, 'store'])->name('bills.pay.store');

    Route::resource('advances', DirectorAdvanceController::class);
    Route::get('advances/{advance}/reimburse', [DirectorReimbursementController::class, 'create'])->name('advances.reimburse.create');
    Route::post('advances/{advance}/reimburse', [DirectorReimbursementController::class, 'store'])->name('advances.reimburse.store');

    Route::resource('reconciliations', ReconciliationController::class);
    Route::post('reconciliations/entries/{entry}/match', [ReconciliationController::class, 'match'])->name('reconciliations.match');
    Route::post('reconciliations/{reconciliation}/complete', [ReconciliationController::class, 'complete'])->name('reconciliations.complete');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/period', [ReportController::class, 'exportPeriod'])->name('reports.export.period');
    Route::get('reports/export/monthly', [ReportController::class, 'exportMonthly'])->name('reports.export.monthly');
    Route::get('reports/export/annual', [ReportController::class, 'exportAnnual'])->name('reports.export.annual');
    Route::get('reports/export/project', [ReportController::class, 'exportProject'])->name('reports.export.project');
    Route::get('reports/export/bills', [ReportController::class, 'exportBills'])->name('reports.export.bills');
    Route::get('reports/export/reconciliation', [ReportController::class, 'exportReconciliation'])->name('reports.export.reconciliation');
});

require __DIR__.'/auth.php';
