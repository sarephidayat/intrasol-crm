<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Services\CashBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    public function index()
    {
        $reconciliations = Reconciliation::with(['bankStatement', 'creator'])
            ->orderByDesc('reconciliation_date')
            ->paginate(15);
        return view('reconciliations.index', compact('reconciliations'));
    }

    public function create(CashBalanceService $service)
    {
        $systemBalance = $service->getCurrentBalance();
        return view('reconciliations.create', compact('systemBalance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reconciliation_date' => ['required', 'date', 'before_or_equal:today'],
            'actual_bank_balance' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $systemBalance = app(CashBalanceService::class)->getCurrentBalance();
        $difference = $validated['actual_bank_balance'] - $systemBalance;

        DB::beginTransaction();
        try {
            $reconciliation = Reconciliation::create([
                'bank_statement_id' => null,
                'reconciliation_date' => $validated['reconciliation_date'],
                'system_balance' => $systemBalance,
                'actual_bank_balance' => $validated['actual_bank_balance'],
                'difference' => $difference,
                'notes' => $validated['notes'],
                'status' => 'completed',
                'created_by' => Auth::id(),
                'completed_at' => now(),
            ]);

            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => Reconciliation::class,
                'auditable_id' => $reconciliation->id,
                'action' => 'reconciled',
                'before_values' => null,
                'after_values' => [
                    'reconciliation_date' => $validated['reconciliation_date'],
                    'system_balance' => $systemBalance,
                    'actual_bank_balance' => $validated['actual_bank_balance'],
                    'difference' => $difference,
                ],
                'reason' => $validated['notes'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('reconciliations.show', $reconciliation)
                ->with('status', 'Rekonsiliasi berhasil disimpan. Selisih tidak dibuatkan transaksi otomatis.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan rekonsiliasi: ' . $e->getMessage()]);
        }
    }

    public function show(Reconciliation $reconciliation)
    {
        $reconciliation->load(['bankStatement', 'creator']);
        return view('reconciliations.show', compact('reconciliation'));
    }

    public function edit(Reconciliation $reconciliation)
    {
        if ($reconciliation->status === 'completed') {
            return back()->withErrors(['reconciliation' => 'Rekonsiliasi yang sudah selesai tidak dapat diedit.']);
        }
        $systemBalance = app(CashBalanceService::class)->getCurrentBalance();
        return view('reconciliations.edit', compact('reconciliation', 'systemBalance'));
    }

    public function update(Request $request, Reconciliation $reconciliation)
    {
        if ($reconciliation->status === 'completed') {
            return back()->withErrors(['reconciliation' => 'Rekonsiliasi yang sudah selesai tidak dapat diedit.']);
        }

        $validated = $request->validate([
            'reconciliation_date' => ['required', 'date', 'before_or_equal:today'],
            'actual_bank_balance' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $systemBalance = app(CashBalanceService::class)->getCurrentBalance();
        $difference = $validated['actual_bank_balance'] - $systemBalance;

        $before = $reconciliation->getAttributes();

        DB::beginTransaction();
        try {
            $reconciliation->update([
                'reconciliation_date' => $validated['reconciliation_date'],
                'system_balance' => $systemBalance,
                'actual_bank_balance' => $validated['actual_bank_balance'],
                'difference' => $difference,
                'notes' => $validated['notes'],
                'updated_by' => Auth::id(),
            ]);

            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => Reconciliation::class,
                'auditable_id' => $reconciliation->id,
                'action' => 'updated',
                'before_values' => $before,
                'after_values' => $reconciliation->fresh()->getAttributes(),
                'reason' => $validated['reason'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('reconciliations.show', $reconciliation)
                ->with('status', 'Rekonsiliasi berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui rekonsiliasi: ' . $e->getMessage()]);
        }
    }

    public function destroy(Reconciliation $reconciliation)
    {
        if ($reconciliation->status === 'completed') {
            return back()->withErrors(['reconciliation' => 'Rekonsiliasi yang sudah selesai tidak dapat dihapus.']);
        }

        DB::beginTransaction();
        try {
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => Reconciliation::class,
                'auditable_id' => $reconciliation->id,
                'action' => 'deleted',
                'before_values' => $reconciliation->getAttributes(),
                'after_values' => null,
                'reason' => 'Rekonsiliasi dihapus oleh pengguna',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            $reconciliation->delete();
            DB::commit();

            return redirect()->route('reconciliations.index')->with('status', 'Rekonsiliasi dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus rekonsiliasi: ' . $e->getMessage()]);
        }
    }
}