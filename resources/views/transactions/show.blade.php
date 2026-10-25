<x-app-layout>
    @section('page-title', 'Detail Transaksi')
    @section('page-subtitle', $transaction->description)

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 font-['Space_Grotesk'] font-semibold">Informasi Transaksi</div>
            <div class="p-4 space-y-2 text-sm">
                <div class="flex justify-between"><span>Tanggal</span><span>{{ $transaction->transaction_date->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span>Jenis</span><span>{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span></div>
                <div class="flex justify-between"><span>Jumlah</span><span>Rp{{ number_format($transaction->amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Kategori</span><span>{{ $transaction->category->name }}</span></div>
                <div class="flex justify-between"><span>Pihak</span><span>{{ $transaction->party->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span>Proyek</span><span>{{ $transaction->project->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span>Dibuat</span><span>{{ $transaction->created_at }}</span></div>
                <div class="flex justify-between"><span>Diarsipkan</span><span>{{ $transaction->archived_at ? $transaction->archived_at : '-' }}</span></div>
                @if($transaction->receipt_path)
                    <div class="pt-2">
                        <a href="{{ route('receipts.view', $transaction) }}" target="_blank" class="underline">Lihat Bukti</a> |
                        <a href="{{ route('receipts.download', $transaction) }}" class="underline">Unduh Bukti</a>
                    </div>
                @endif
            </div>
            <div class="p-4 flex gap-2">
                <a href="{{ route('transactions.edit', $transaction) }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Koreksi</a>
                @if(!$transaction->archived_at)
                    <form method="POST" action="{{ route('transactions.archive', $transaction) }}" onsubmit="return confirm('Arsipkan transaksi ini?');">
                        @csrf
                        <input type="hidden" name="reason" value="Arsip melalui UI">
                        <button class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Arsipkan</button>
                    </form>
                @endif
                <a href="{{ route('transactions.index') }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Kembali</a>
            </div>
        </div>
        <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 font-['Space_Grotesk'] font-semibold">Riwayat Audit</div>
            <div class="p-4 space-y-2 text-xs">
                @forelse($transaction->auditLogs->sortByDesc('created_at') as $log)
                    <div class="border border-[#DDDDDD] p-2">
                        <div class="font-semibold">{{ $log->action }} - {{ $log->created_at }}</div>
                        <div>Oleh: {{ $log->user->name ?? '-' }}</div>
                        @if($log->reason)<div>Alasan: {{ $log->reason }}</div>@endif
                    </div>
                @empty
                    <div class="text-[#171717]/70">Tidak ada riwayat</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
