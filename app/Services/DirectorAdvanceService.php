<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\CashTransaction;
use App\Models\Category;
use App\Models\DirectorAdvance;
use App\Models\DirectorReimbursement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DirectorAdvanceService
{
    public function reimburse(DirectorAdvance $advance, array $data): DirectorReimbursement
    {
        return DB::transaction(function () use ($advance, $data) {
            $advance = DirectorAdvance::lockForUpdate()->findOrFail($advance->id);
            $amount = (int) $data['amount'];
            if ($amount <= 0) {
                throw new \InvalidArgumentException('Jumlah penggantian harus lebih dari 0.');
            }
            if ($amount > $advance->outstanding_amount) {
                throw new \InvalidArgumentException('Jumlah penggantian melebihi sisa kewajiban.');
            }

            $category = Category::where('type', 'expense')->where('is_active', true)->first();
            if (! $category) {
                throw new \InvalidArgumentException('Kategori pengeluaran tidak ditemukan.');
            }

            $tx = CashTransaction::create([
                'transaction_date' => $data['payment_date'],
                'type' => 'expense',
                'amount' => $amount,
                'category_id' => $category->id,
                'project_id' => $advance->project_id,
                'description' => 'Reimburs talangan '.$advance->reference.' - '.$advance->person_name,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $reimb = DirectorReimbursement::create([
                'reference' => 'DR-'.strtoupper(Str::random(8)),
                'director_advance_id' => $advance->id,
                'cash_transaction_id' => $tx->id,
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $reimbursed = $advance->reimbursed_amount + $amount;
            $outstanding = $advance->amount - $reimbursed;
            if ($outstanding < 0) {
                $outstanding = 0;
            }
            $status = $outstanding == 0 ? 'reimbursed' : 'partially_reimbursed';
            $advance->update([
                'reimbursed_amount' => $reimbursed,
                'outstanding_amount' => $outstanding,
                'status' => $status,
                'updated_by' => Auth::id(),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => DirectorAdvance::class,
                'auditable_id' => $advance->id,
                'action' => 'reimbursed',
                'before_values' => ['reimbursed_amount' => $advance->reimbursed_amount - $amount, 'outstanding_amount' => $advance->outstanding_amount + $amount],
                'after_values' => ['reimbursed_amount' => $reimbursed, 'outstanding_amount' => $outstanding, 'status' => $status],
                'reason' => $data['notes'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $reimb;
        });
    }
}
