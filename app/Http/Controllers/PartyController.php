<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $query = Party::query();
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        $parties = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('parties.index', compact('parties'));
    }

    public function create()
    {
        return view('parties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:client,vendor,both'],
            'primary_pic_name' => ['nullable', 'string', 'max:255'],
            'primary_pic_email' => ['nullable', 'email', 'max:255'],
            'primary_pic_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        Party::create($validated);
        return redirect()->route('parties.index')->with('status', 'Pihak berhasil ditambahkan.');
    }

    public function show(Party $party)
    {
        $party->load('cashTransactions.category', 'projects');
        return view('parties.show', compact('party'));
    }

    public function edit(Party $party)
    {
        return view('parties.edit', compact('party'));
    }

    public function update(Request $request, Party $party)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:client,vendor,both'],
            'primary_pic_name' => ['nullable', 'string', 'max:255'],
            'primary_pic_email' => ['nullable', 'email', 'max:255'],
            'primary_pic_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $party->update($validated);
        return redirect()->route('parties.index')->with('status', 'Pihak berhasil diperbarui.');
    }

    public function destroy(Party $party)
    {
        if ($party->cashTransactions()->exists() || $party->projects()->exists()) {
            return back()->withErrors(['party' => 'Tidak dapat mengarsipkan pihak yang masih terkait transaksi/proyek.']);
        }
        $party->delete();
        return redirect()->route('parties.index')->with('status', 'Pihak diarsipkan.');
    }
}
