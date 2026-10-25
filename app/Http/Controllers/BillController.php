<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Party;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with(['party', 'project'])->whereNull('archived_at');
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('bill_number', 'like', '%'.$request->q.'%')
                    ->orWhere('description', 'like', '%'.$request->q.'%');
            });
        }
        $bills = $query->orderByDesc('issue_date')->paginate(15)->withQueryString();
        return view('bills.index', compact('bills'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'receivable');
        $parties = $type === 'receivable'
            ? Party::whereIn('role', ['client', 'both'])->where('is_active', true)->orderBy('name')->get()
            : Party::whereIn('role', ['vendor', 'both'])->where('is_active', true)->orderBy('name')->get();
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        return view('bills.create', compact('type', 'parties', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:receivable,payable'],
            'party_id' => ['required', 'exists:parties,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $party = Party::findOrFail($validated['party_id']);
        if ($validated['type'] === 'receivable' && ! in_array($party->role, ['client', 'both'])) {
            return back()->withInput()->withErrors(['party_id' => 'Piutang hanya untuk klien.']);
        }
        if ($validated['type'] === 'payable' && ! in_array($party->role, ['vendor', 'both'])) {
            return back()->withInput()->withErrors(['party_id' => 'Utang hanya untuk vendor.']);
        }

        $bill = Bill::create([
            'type' => $validated['type'],
            'bill_number' => strtoupper($validated['type'][0]).'-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
            'party_id' => $validated['party_id'],
            'project_id' => $validated['project_id'] ?? null,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'paid_amount' => 0,
            'outstanding_amount' => $validated['amount'],
            'status' => now()->toDateString() > $validated['due_date'] ? 'overdue' : 'unpaid',
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('bills.show', $bill)->with('status', 'Tagihan berhasil dibuat.');
    }

    public function show(Bill $bill)
    {
        $bill->load(['party', 'project', 'payments.cashTransaction', 'payments.creator']);
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        if ($bill->payments()->exists() || $bill->status === 'paid') {
            return back()->withErrors(['bill' => 'Tagihan yang sudah ada pembayaran tidak dapat diedit.']);
        }
        $parties = $bill->type === 'receivable'
            ? Party::whereIn('role', ['client', 'both'])->where('is_active', true)->orderBy('name')->get()
            : Party::whereIn('role', ['vendor', 'both'])->where('is_active', true)->orderBy('name')->get();
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        return view('bills.edit', compact('bill', 'parties', 'projects'));
    }

    public function update(Request $request, Bill $bill)
    {
        if ($bill->payments()->exists() || $bill->status === 'paid') {
            return back()->withErrors(['bill' => 'Tagihan yang sudah ada pembayaran tidak dapat diedit.']);
        }
        $validated = $request->validate([
            'party_id' => ['required', 'exists:parties,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);
        $bill->update([
            'party_id' => $validated['party_id'],
            'project_id' => $validated['project_id'] ?? null,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'outstanding_amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
            'status' => now()->toDateString() > $validated['due_date'] ? 'overdue' : 'unpaid',
            'updated_by' => Auth::id(),
        ]);
        return redirect()->route('bills.show', $bill)->with('status', 'Tagihan diperbarui.');
    }

    public function archive(Request $request, Bill $bill)
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        if ($bill->payments()->exists()) {
            return back()->withErrors(['bill' => 'Tagihan dengan riwayat pembayaran tidak dapat diarsipkan tanpa alur koreksi khusus.']);
        }
        $bill->update(['archived_at' => now(), 'status' => 'archived', 'updated_by' => Auth::id()]);
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => Bill::class,
            'auditable_id' => $bill->id,
            'action' => 'archived',
            'before_values' => ['status' => 'unpaid'],
            'after_values' => ['status' => 'archived'],
            'reason' => $validated['reason'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
        return redirect()->route('bills.index')->with('status', 'Tagihan diarsipkan.');
    }
}
