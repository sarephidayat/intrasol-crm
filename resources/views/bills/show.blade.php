<x-app-layout>
    @section('page-title', 'Detail Tagihan')
    @section('page-subtitle', $bill->bill_number)

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 font-['Space_Grotesk'] font-semibold">Informasi Tagihan</div>
            <div class="p-4 space-y-2 text-sm">
                <div class="flex justify-between"><span>Jenis</span><span>{{ $bill->type === 'receivable' ? 'Piutang' : 'Utang' }}</span></div>
                <div class="flex justify-between"><span>Pihak</span><span>{{ $bill->party->name }}</span></div>
                <div class="flex justify-between"><span>Tanggal Terbit</span><span>{{ $bill->issue_date->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span>Jatuh Tempo</span><span>{{ $bill->due_date->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span>Jumlah</span><span>Rp{{ number_format($bill->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Dibayar</span><span>Rp{{ number_format($bill->paid_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Sisa</span><span>Rp{{ number_format($bill->outstanding_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Status</span><span>{{ $bill->status }}</span></div>
                <div class="pt-2">{{ $bill->description }}</div>
            </div>
            <div class="p-4 flex gap-2">
                @if($bill->outstanding_amount > 0 && !$bill->archived_at)
                    <a href="{{ route('bills.pay.create', $bill) }}" class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Bayar</a>
                @endif
                <a href="{{ route('bills.index', ['type'=>$bill->type]) }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Kembali</a>
            </div>
        </div>
        <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 font-['Space_Grotesk'] font-semibold">Riwayat Pembayaran</div>
            <div class="p-4 space-y-2 text-xs">
                @forelse($bill->payments as $pay)
                    <div class="border border-[#DDDDDD] p-2">
                        <div class="font-semibold">{{ $pay->reference }} - {{ $pay->payment_date->format('d/m/Y') }}</div>
                        <div>Rp{{ number_format($pay->amount, 0, ',', '.') }} ({{ $pay->payment_method }})</div>
                    </div>
                @empty
                    <div class="text-[#171717]/70">Belum ada pembayaran</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
