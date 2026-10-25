<?php

namespace App\Http\Controllers;

use App\Models\DirectorAdvance;
use App\Models\Party;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DirectorAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $query = DirectorAdvance::with(['project']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $advances = $query->orderByDesc('advance_date')->paginate(15)->withQueryString();
        return view('advances.index', compact('advances'));
    }

    public function create()
    {
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        return view('advances.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_name' => ['required', 'string', 'max:255'],
            'advance_date' => ['required', 'date'],
            'amount' => ['required', 'integer', 'min:1'],
            'purpose' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'notes' => ['nullable', 'string'],
        ]);

        DirectorAdvance::create([
            'reference' => 'DA-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
            'person_name' => $validated['person_name'],
            'advance_date' => $validated['advance_date'],
            'amount' => $validated['amount'],
            'reimbursed_amount' => 0,
            'outstanding_amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'project_id' => $validated['project_id'] ?? null,
            'status' => 'open',
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('advances.index')->with('status', 'Talangan berhasil dicatat.');
    }

    public function show(DirectorAdvance $advance)
    {
        $advance->load(['project', 'items.category', 'reimbursements.cashTransaction']);
        return view('advances.show', compact('advance'));
    }

    public function edit(DirectorAdvance $advance)
    {
        $projects = Project::where('is_active', true)->orderBy('name')->get();
        return view('advances.edit', compact('advance', 'projects'));
    }

    public function update(Request $request, DirectorAdvance $advance)
    {
        $validated = $request->validate([
            'person_name' => ['required', 'string', 'max:255'],
            'advance_date' => ['required', 'date'],
            'amount' => ['required', 'integer', 'min:1'],
            'purpose' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'notes' => ['nullable', 'string'],
        ]);
        $advance->update([
            'person_name' => $validated['person_name'],
            'advance_date' => $validated['advance_date'],
            'amount' => $validated['amount'],
            'outstanding_amount' => $validated['amount'] - $advance->reimbursed_amount,
            'purpose' => $validated['purpose'],
            'project_id' => $validated['project_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'updated_by' => Auth::id(),
        ]);
        return redirect()->route('advances.show', $advance)->with('status', 'Talangan diperbarui.');
    }
}
