<?php

namespace App\Http\Controllers;

use App\Models\DirectorAdvance;
use App\Services\DirectorAdvanceService;
use Illuminate\Http\Request;

class DirectorReimbursementController extends Controller
{
    public function create(DirectorAdvance $advance)
    {
        if ($advance->outstanding_amount <= 0) {
            return back()->withErrors(['advance' => 'Tidak ada sisa kewajiban untuk direimburse.']);
        }
        return view('advances.reimburse', compact('advance'));
    }

    public function store(Request $request, DirectorAdvance $advance, DirectorAdvanceService $service)
    {
        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $service->reimburse($advance, $validated);
            return redirect()->route('advances.show', $advance)->with('status', 'Penggantian berhasil dicatat, kas berkurang dan kewajiban berkurang.');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['reimburse' => $e->getMessage()]);
        }
    }
}
