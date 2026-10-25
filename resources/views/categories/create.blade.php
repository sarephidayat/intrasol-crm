<x-app-layout>
    @section('page-title', 'Tambah Kategori')
    @section('page-subtitle', $type === 'income' ? 'Kategori Pemasukan' : 'Kategori Pengeluaran')

    <div class="max-w-xl border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
        <form method="POST" action="{{ route('categories.store') }}" class="p-4 space-y-3">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <div><label class="block text-sm mb-1">Nama Kategori *</label><input name="name" value="{{ old('name') }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
            <div><label class="block text-sm mb-1">Deskripsi</label><textarea name="description" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('description') }}</textarea></div>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
            <div class="flex gap-2">
                <button class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Simpan</button>
                <a href="{{ route('categories.index', ['type'=>$type]) }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
