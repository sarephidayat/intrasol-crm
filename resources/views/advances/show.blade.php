<x-app-layout>
    @section('page-title', 'Detail Talangan')
    @section('page-subtitle', $advance->reference)

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 font-['Space_Grotesk'] font-semibold">Informasi Talangan</div>
            <div class="p-4 space-y-2 text-sm">
                <div class="flex justify-between"><span>Penerima</span><span>{{ $advance->person_name }}</span></div>
                <div class="flex justify-between"><span>Tanggal</span><span>{{ $advance->advance_date->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span>Tujuan</span><span>{{ $advance->purpose }}</span></div>
                <div class="flex justify-between"><span>Jumlah</span><span>Rp{{ number_format($advance->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Dikembalikan</span><span>Rp{{ number_format($advance->reimbursed_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Sisa Kewajiban</span><span>Rp{{ number_format($advance->outstanding_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Status</span><span>{{ $advance->status }}</span></div>
            </div>
            <div class="p-4 flex gap-2">
                @if($advance->outstanding_amount > 0)
                    <a href="{{ route('advances.reimburse.create', $advance) }}" class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Reimburse</a>
                @endif
                <a href="{{ route('advances.index') }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Kembali</a>
            </div>
        </div>
        <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 font-['Space_Grotesk'] font-semibold">Riwayat Penggantian</div>
            <div class="p-4 space-y-2 text-xs">
                @forelse($advance->reimbursements as $r)
                    <div class="border border-[#DDDDDD] p-2">
                        <div class="font-semibold">{{ $r->reference }} - {{ $r->payment_date->format('d/m/Y') }}</div>
                        <div>Rp{{ number_format($r->amount, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <div class="text-[#171717]/70">Belum ada penggantian</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
