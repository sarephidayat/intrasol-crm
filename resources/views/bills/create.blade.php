<x-app-layout>
    @section('page-title', request('type') === 'payable' ? 'Tambah Utang' : 'Tambah Piutang')
    @section('page-subtitle', 'Tagihan')

    <div class="max-w-2xl border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
        <form method="POST" action="{{ route('bills.store') }}" class="p-4 space-y-3">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <div>
                <label class="block text-sm mb-1">Pihak *</label>
                <select name="party_id" required class="w-full border-2 border-[#171717] px-3 py-2">
                    @foreach($parties as $p)
                        <option value="{{ $p->id }}" @selected(old('party_id')==$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="block text-sm mb-1">Tanggal Terbit *</label><input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
                <div><label class="block text-sm mb-1">Jatuh Tempo *</label><input type="date" name="due_date" value="{{ old('due_date') }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
            </div>
            <div>
                <label class="block text-sm mb-1">Keterangan *</label>
                <input name="description" value="{{ old('description') }}" required class="w-full border-2 border-[#171717] px-3 py-2">
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
            <div><label class="block text-sm mb-1">Catatan</label><textarea name="notes" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('notes') }}</textarea></div>
            <div class="flex gap-2">
                <button class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Simpan</button>
                <a href="{{ route('bills.index', ['type'=>$type]) }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
