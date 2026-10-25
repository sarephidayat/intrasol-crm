<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Services\BillPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillPaymentController extends Controller
{
    public function create(Bill $bill)
    {
        if ($bill->archived_at || $bill->status === 'paid' || $bill->outstanding_amount <= 0) {
            return back()->withErrors(['bill' => 'Tagihan tidak dapat dibayar.']);
        }
        return view('bills.pay', compact('bill'));
    }

    public function store(Request $request, Bill $bill, BillPaymentService $service)
    {
        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $service->payBill($bill, $validated);
            return redirect()->route('bills.show', $bill)->with('status', 'Pembayaran berhasil dicatat dan transaksi kas terhubung.');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['payment' => $e->getMessage()]);
        }
    }
}
