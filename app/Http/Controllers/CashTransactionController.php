<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CashTransaction;
use App\Models\Category;
use App\Models\Party;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = CashTransaction::with(['category', 'party', 'project'])->active();
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->to);
        }
        if ($request->filled('q')) {
            $query->where('description', 'like', '%'.$request->q.'%');
        }
        $transactions = $query->orderByDesc('transaction_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $parties = Party::where('is_active', true)->orderBy('name')->get();
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        return view('transactions.index', compact('transactions', 'categories', 'parties', 'projects'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        $parties = Party::where('is_active', true)->orderBy('name')->get();
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        return view('transactions.create', compact('categories', 'parties', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'party_id' => ['nullable', 'exists:parties,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'description' => ['required', 'string', 'max:255'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $category = Category::findOrFail($validated['category_id']);
        if ($category->type !== $validated['type']) {
            return back()->withInput()->withErrors(['category_id' => 'Jenis kategori tidak sesuai dengan jenis transaksi.']);
        }
        if ($category->is_active === false) {
            return back()->withInput()->withErrors(['category_id' => 'Kategori tidak aktif.']);
        }

        if ($request->filled('project_related') && empty($validated['project_id'])) {
            return back()->withInput()->withErrors(['project_id' => 'Proyek wajib diisi jika transaksi terkait proyek.']);
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'private');
        }

        DB::beginTransaction();
        try {
            $tx = CashTransaction::create([
                'transaction_date' => $validated['transaction_date'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'category_id' => $validated['category_id'],
                'party_id' => $validated['party_id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'description' => $validated['description'],
                'receipt_path' => $receiptPath,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => CashTransaction::class,
                'auditable_id' => $tx->id,
                'action' => 'created',
                'before_values' => null,
                'after_values' => $tx->toArray(),
                'reason' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($receiptPath) {
                Storage::disk('private')->delete($receiptPath);
            }
            throw $e;
        }

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil disimpan.');
    }

    public function show(CashTransaction $transaction)
    {
        $transaction->load(['category', 'party', 'project', 'auditLogs.user']);
        return view('transactions.show', compact('transaction'));
    }

    public function edit(CashTransaction $transaction)
    {
        $categories = Category::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        $parties = Party::where('is_active', true)->orderBy('name')->get();
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'categories', 'parties', 'projects'));
    }

    public function update(Request $request, CashTransaction $transaction)
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'party_id' => ['nullable', 'exists:parties,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'description' => ['required', 'string', 'max:255'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $category = Category::findOrFail($validated['category_id']);
        if ($category->type !== $validated['type']) {
            return back()->withInput()->withErrors(['category_id' => 'Jenis kategori tidak sesuai dengan jenis transaksi.']);
        }
        if ($category->is_active === false) {
            return back()->withInput()->withErrors(['category_id' => 'Kategori tidak aktif.']);
        }

        if ($request->filled('project_related') && empty($validated['project_id'])) {
            return back()->withInput()->withErrors(['project_id' => 'Proyek wajib diisi jika transaksi terkait proyek.']);
        }

        $before = $transaction->getAttributes();
        $data = [
            'transaction_date' => $validated['transaction_date'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'category_id' => $validated['category_id'],
            'party_id' => $validated['party_id'] ?? null,
            'project_id' => $validated['project_id'] ?? null,
            'description' => $validated['description'],
            'updated_by' => Auth::id(),
        ];

        if ($request->hasFile('receipt')) {
            if ($transaction->receipt_path) {
                Storage::disk('private')->delete($transaction->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'private');
        }

        DB::beginTransaction();
        try {
            $transaction->update($data);
            AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => CashTransaction::class,
                'auditable_id' => $transaction->id,
                'action' => 'updated',
                'before_values' => $before,
                'after_values' => $transaction->fresh()->getAttributes(),
                'reason' => $validated['reason'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('transactions.show', $transaction)->with('status', 'Transaksi berhasil diperbarui.');
    }

    public function archive(Request $request, CashTransaction $transaction)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        if ($transaction->archived_at) {
            return back()->withErrors(['transaction' => 'Transaksi sudah diarsipkan.']);
        }

        $before = $transaction->getAttributes();
        DB::beginTransaction();
        try {
            $transaction->update(['archived_at' => now(), 'updated_by' => Auth::id()]);
            AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => CashTransaction::class,
                'auditable_id' => $transaction->id,
                'action' => 'archived',
                'before_values' => $before,
                'after_values' => $transaction->fresh()->getAttributes(),
                'reason' => $validated['reason'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('transactions.index')->with('status', 'Transaksi diarsipkan.');
    }
}
