<x-app-layout>
    @section('page-title', 'Rekonsiliasi Bank')
    @section('page-subtitle', 'Bank Jago')

    <div class="page-header">
        <a href="{{ route('reconciliations.create') }}" class="btn-primary whitespace-nowrap">+ Buat Rekonsiliasi</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table min-w-[640px]">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-right">Saldo Sistem</th>
                        <th class="text-right">Saldo Aktual</th>
                        <th class="text-right">Selisih</th>
                        <th>Status</th>
                        <th class="hidden sm:table-cell">Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reconciliations as $r)
                        <tr>
                            <td class="whitespace-nowrap">{{ $r->reconciliation_date->translatedFormat('d M Y') }}</td>
                            <td class="text-right">Rp{{ number_format($r->system_balance, 0, ',', '.') }}</td>
                            <td class="text-right">Rp{{ number_format($r->actual_bank_balance, 0, ',', '.') }}</td>
                            <td class="text-right {{ $r->difference === 0 ? 'text-[#166534]' : ($r->difference > 0 ? 'text-[#166534]' : 'text-[#B42318]') }}">
                                {{ $r->difference >= 0 ? '+' : '' }}Rp{{ number_format(abs($r->difference), 0, ',', '.') }}
                            </td>
                            <td>
                                @if($r->status === 'completed')
                                    <span class="badge-success">Selesai</span>
                                @elseif($r->status === 'draft')
                                    <span class="badge-warning">Draft</span>
                                @else
                                    <span class="badge-info">{{ $r->status }}</span>
                                @endif
                            </td>
                            <td class="hidden sm:table-cell">{{ $r->creator->name ?? 'System' }}</td>
                            <td class="text-right">
                                <a href="{{ route('reconciliations.show', $r) }}" class="underline text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-6 text-center text-[#171717]/70">Belum ada data rekonsiliasi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-t-2 border-[#171717] bg-[#F7F5EF]">
            <div class="table-responsive">{{ $reconciliations->links() }}</div>
        </div>
    </div>
</x-app-layout>