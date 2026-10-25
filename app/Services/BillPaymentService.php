<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\CashTransaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillPaymentService
{
    public function payBill(Bill $bill, array $data): BillPayment
    {
        return DB::transaction(function () use ($bill, $data) {
            $bill = Bill::lockForUpdate()->findOrFail($bill->id);
            $amount = (int) $data['amount'];
            if ($amount <= 0) {
                throw new \InvalidArgumentException('Jumlah pembayaran harus lebih dari 0.');
            }
            if ($bill->archived_at || $bill->status === 'paid') {
                throw new \InvalidArgumentException('Tagihan tidak dapat dibayar.');
            }
            if ($amount > $bill->outstanding_amount) {
                throw new \InvalidArgumentException('Jumlah pembayaran melebihi sisa tagihan.');
            }

            $categoryType = $bill->type === 'receivable' ? 'income' : 'expense';
            $category = Category::where('type', $categoryType)->where('is_active', true)->first();
            if (! $category) {
                throw new \InvalidArgumentException('Kategori tidak ditemukan untuk jenis pembayaran.');
            }

            $tx = CashTransaction::create([
                'transaction_date' => $data['payment_date'],
                'type' => $categoryType,
                'amount' => $amount,
                'category_id' => $category->id,
                'party_id' => $bill->party_id,
                'project_id' => $bill->project_id,
                'description' => ($bill->type === 'receivable' ? 'Pembayaran piutang ' : 'Pembayaran utang ').$bill->bill_number,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $payment = BillPayment::create([
                'reference' => 'BP-'.strtoupper(Str::random(8)),
                'bill_id' => $bill->id,
                'cash_transaction_id' => $tx->id,
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'payment_reference' => $data['payment_reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $paid = $bill->paid_amount + $amount;
            $outstanding = $bill->amount - $paid;
            if ($outstanding < 0) {
                $outstanding = 0;
            }
            $status = $outstanding == 0 ? 'paid' : 'partially_paid';
            if ($bill->due_date->isPast() && $outstanding > 0) {
                $status = 'overdue';
            }
            $bill->update([
                'paid_amount' => $paid,
                'outstanding_amount' => $outstanding,
                'status' => $status,
                'updated_by' => Auth::id(),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => Bill::class,
                'auditable_id' => $bill->id,
                'action' => 'payment_recorded',
                'before_values' => ['paid_amount' => $bill->paid_amount - $amount, 'outstanding_amount' => $bill->outstanding_amount + $amount],
                'after_values' => ['paid_amount' => $paid, 'outstanding_amount' => $outstanding, 'status' => $status],
                'reason' => $data['notes'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $payment;
        });
    }
}
