<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'expense');
        $categories = Category::where('type', $type)->orderBy('name')->paginate(15)->withQueryString();
        return view('categories.index', compact('categories', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'expense');
        return view('categories.create', compact('type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $exists = Category::where('name', $validated['name'])->where('type', $validated['type'])->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['name' => 'Kategori dengan nama dan jenis yang sama sudah ada.']);
        }
        Category::create($validated);
        return redirect()->route('categories.index', ['type' => $validated['type']])->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function show(Category $category)
    {
        $category->load('cashTransactions');
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $exists = Category::where('name', $validated['name'])->where('type', $validated['type'])->where('id', '!=', $category->id)->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['name' => 'Kategori dengan nama dan jenis yang sama sudah ada.']);
        }
        $category->update($validated);
        return redirect()->route('categories.index', ['type' => $validated['type']])->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->cashTransactions()->exists()) {
            return back()->withErrors(['category' => 'Tidak dapat mengarsipkan kategori yang masih digunakan transaksi.']);
        }
        $category->delete();
        return redirect()->route('categories.index', ['type' => $category->type])->with('status', 'Kategori diarsipkan.');
    }
}
