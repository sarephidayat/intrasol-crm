<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptController extends Controller
{
    public function show(CashTransaction $transaction)
    {
        if (! Auth::check()) {
            abort(403);
        }
        if (! $transaction->receipt_path) {
            abort(404);
        }
        if (! Storage::disk('private')->exists($transaction->receipt_path)) {
            abort(404);
        }
        $path = storage_path('app/private/'.$transaction->receipt_path);
        return response()->file($path);
    }

    public function download(CashTransaction $transaction)
    {
        if (! Auth::check()) {
            abort(403);
        }
        if (! $transaction->receipt_path) {
            abort(404);
        }
        if (! Storage::disk('private')->exists($transaction->receipt_path)) {
            abort(404);
        }
        return Storage::disk('private')->download($transaction->receipt_path);
    }
}
