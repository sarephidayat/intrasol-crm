<x-app-layout>
    @section('page-title', 'Catat Talangan')
    @section('page-subtitle', 'Dana Talangan')

    <div class="max-w-2xl border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
        <form method="POST" action="{{ route('advances.store') }}" class="p-4 space-y-3">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="block text-sm mb-1">Nama Penerima *</label><input name="person_name" value="{{ old('person_name') }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
                <div><label class="block text-sm mb-1">Tanggal *</label><input type="date" name="advance_date" value="{{ old('advance_date', now()->toDateString()) }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="block text-sm mb-1">Jumlah (IDR) *</label><input type="number" name="amount" min="1" step="1" value="{{ old('amount') }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
                <div>
                    <label class="block text-sm mb-1">Proyek</label>
                    <select name="project_id" class="w-full border-2 border-[#171717] px-3 py-2">
                        <option value="">-</option>
                        @foreach($projects as $pr)
                            <option value="{{ $pr->id }}" @selected(old('project_id')==$pr->id)>{{ $pr->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div><label class="block text-sm mb-1">Tujuan/Keterangan *</label><input name="purpose" value="{{ old('purpose') }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
            <div><label class="block text-sm mb-1">Catatan</label><textarea name="notes" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('notes') }}</textarea></div>
            <div class="text-xs text-[#171717]/70">Talangan tidak mengurangi saldo kas saat dicatat. Penggantian akan mengurangi kas dan kewajiban.</div>
            <div class="flex gap-2">
                <button class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Simpan</button>
                <a href="{{ route('advances.index') }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
