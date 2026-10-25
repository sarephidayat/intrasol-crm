<x-app-layout>
    @section('page-title', 'Edit Kategori')
    @section('page-subtitle', $category->name)

    <div class="max-w-xl border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
        <form method="POST" action="{{ route('categories.update', $category) }}" class="p-4 space-y-3">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="{{ $category->type }}">
            <div><label class="block text-sm mb-1">Nama Kategori *</label><input name="name" value="{{ old('name', $category->name) }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
            <div><label class="block text-sm mb-1">Deskripsi</label><textarea name="description" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('description', $category->description) }}</textarea></div>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Aktif</label>
            <div class="flex gap-2">
                <button class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Simpan</button>
                <a href="{{ route('categories.index', ['type'=>$category->type]) }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
