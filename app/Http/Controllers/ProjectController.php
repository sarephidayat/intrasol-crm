<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('party')->withCount(['cashTransactions as income_sum' => function($q){
            $q->active()->where('type','income')->select(\DB::raw('coalesce(sum(amount),0)'));
        }])->withCount(['cashTransactions as expense_sum' => function($q){
            $q->active()->where('type','expense')->select(\DB::raw('coalesce(sum(amount),0)'));
        }]);
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%')->orWhere('code', 'like', '%'.$request->q.'%');
        }
        $projects = $query->orderBy('name')->paginate(15)->withQueryString();

        // Load receivables for each project
        $projectIds = $projects->pluck('id');
        $receivables = \App\Models\Bill::where('type', 'receivable')
            ->whereNull('archived_at')
            ->whereIn('project_id', $projectIds)
            ->selectRaw('project_id, sum(outstanding_amount) as total_receivable')
            ->groupBy('project_id')
            ->pluck('total_receivable', 'project_id')
            ->toArray();

        return view('projects.index', compact('projects', 'receivables'));
    }

    public function create()
    {
        $parties = Party::where('is_active', true)->orderBy('name')->get();
        return view('projects.create', compact('parties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:projects,code'],
            'name' => ['required', 'string', 'max:255'],
            'party_id' => ['nullable', 'exists:parties,id'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['status'] = $validated['status'] ?? 'active';
        Project::create($validated);
        return redirect()->route('projects.index')->with('status', 'Proyek berhasil ditambahkan.');
    }

    public function show(Project $project)
    {
        $project->load('party');

        $transactions = $project->cashTransactions()
            ->active()
            ->with(['category', 'party'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');

        $devCategoryIds = \App\Models\Category::whereIn('name', ['Gaji Developer', 'Salary'])
            ->where('type', 'expense')
            ->pluck('id')
            ->toArray();

        $salesCategoryIds = \App\Models\Category::whereIn('name', ['Operational', 'Project Expense'])
            ->where('type', 'expense')
            ->pluck('id')
            ->toArray();

        $devPayments = $transactions->where('type', 'expense')->whereIn('category_id', $devCategoryIds)->sum('amount');
        $salesPayments = $transactions->where('type', 'expense')->whereIn('category_id', $salesCategoryIds)->sum('amount');
        $otherExpenses = $transactions->where('type', 'expense')->whereNotIn('category_id', array_merge($devCategoryIds, $salesCategoryIds))->sum('amount');

        $receivables = \App\Models\Bill::where('type', 'receivable')
            ->where('project_id', $project->id)
            ->whereNull('archived_at')
            ->sum('outstanding_amount');

        return view('projects.show', compact(
            'project',
            'transactions',
            'income',
            'expense',
            'devPayments',
            'salesPayments',
            'otherExpenses',
            'receivables'
        ));
    }

    public function edit(Project $project)
    {
        $parties = Party::where('is_active', true)->orderBy('name')->get();
        return view('projects.edit', compact('project','parties'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:projects,code,'.$project->id],
            'name' => ['required', 'string', 'max:255'],
            'party_id' => ['nullable', 'exists:parties,id'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $project->update($validated);
        return redirect()->route('projects.index')->with('status', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        if ($project->cashTransactions()->exists()) {
            return back()->withErrors(['project' => 'Tidak dapat mengarsipkan proyek yang masih terkait transaksi.']);
        }
        $project->delete();
        return redirect()->route('projects.index')->with('status', 'Proyek diarsipkan.');
    }
}
