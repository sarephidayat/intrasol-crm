<x-app-layout>
    @section('page-title', 'Tambah Proyek')
    @section('page-subtitle', 'Proyek')

    <div class="max-w-2xl border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
        <form method="POST" action="{{ route('projects.store') }}" class="p-4 space-y-3">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="block text-sm mb-1">Kode Proyek *</label><input name="code" value="{{ old('code') }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
                <div><label class="block text-sm mb-1">Nama Proyek *</label><input name="name" value="{{ old('name') }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
            </div>
            <div>
                <label class="block text-sm mb-1">Klien/Pihak</label>
                <select name="party_id" class="w-full border-2 border-[#171717] px-3 py-2">
                    <option value="">-</option>
                    @foreach($parties as $p)
                        <option value="{{ $p->id }}" @selected(old('party_id')==$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm mb-1">Deskripsi</label><textarea name="description" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('description') }}</textarea></div>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
            <div class="flex gap-2">
                <button class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Simpan</button>
                <a href="{{ route('projects.index') }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
