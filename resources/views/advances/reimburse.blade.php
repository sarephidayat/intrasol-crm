<x-app-layout>
    @section('page-title', 'Reimburse Talangan')
    @section('page-subtitle', $advance->reference)

    <div class="max-w-xl border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
        <div class="p-4 border-b-2 border-[#171717] text-sm">
            <div class="flex justify-between"><span>Sisa Kewajiban</span><span>Rp{{ number_format($advance->outstanding_amount, 0, ',', '.') }}</span></div>
        </div>
        <form method="POST" action="{{ route('advances.reimburse.store', $advance) }}" class="p-4 space-y-3">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="block text-sm mb-1">Tanggal Bayar *</label><input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
                <div><label class="block text-sm mb-1">Jumlah (IDR) *</label><input type="number" name="amount" min="1" max="{{ $advance->outstanding_amount }}" step="1" value="{{ old('amount', $advance->outstanding_amount) }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
            </div>
            <div><label class="block text-sm mb-1">Metode Pembayaran *</label><input name="payment_method" value="{{ old('payment_method', 'bank_transfer') }}" required class="w-full border-2 border-[#171717] px-3 py-2"></div>
            <div><label class="block text-sm mb-1">Catatan</label><textarea name="notes" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('notes') }}</textarea></div>
            <div class="text-xs text-[#171717]/70">Penggantian akan membuat transaksi kas keluar (pengeluaran) dan mengurangi kewajiban talangan. Tidak menggandakan beban operasional.</div>
            <div class="flex gap-2">
                <button class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Catat Penggantian</button>
                <a href="{{ route('advances.show', $advance) }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
