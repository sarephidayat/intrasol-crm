<x-app-layout>
    @section('page-title', 'Tagihan')
    @section('page-subtitle', request('type') === 'payable' ? 'Daftar Utang (Payable)' : 'Daftar Piutang (Receivable)')

    <div class="page-header">
        <form method="GET" class="filter-form flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <input name="q" value="{{ request('q') }}" placeholder="Cari nomor/keterangan..." class="form-input flex-1 min-w-[200px]">
            <select name="status" class="form-select min-w-[150px]">
                <option value="">Semua status</option>
                <option value="unpaid" @selected(request('status')==='unpaid')>Belum Bayar</option>
                <option value="partially_paid" @selected(request('status')==='partially_paid')>Sebagian</option>
                <option value="paid" @selected(request('status')==='paid')>Lunas</option>
                <option value="overdue" @selected(request('status')==='overdue')>Terlambat</option>
            </select>
            <button class="btn-secondary whitespace-nowrap">Filter</button>
        </form>
        <a href="{{ route('bills.create', ['type' => request('type', 'receivable')]) }}" class="btn-primary whitespace-nowrap">+ Tambah {{ request('type') === 'payable' ? 'Utang' : 'Piutang' }}</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table min-w-[680px]">
                <thead>
                    <tr>
                        <th>No. Tagihan</th>
                        <th>Tgl Jatuh Tempo</th>
                        <th class="hidden md:table-cell">Pihak</th>
                        <th class="hidden lg:table-cell">Proyek</th>
                        <th class="text-right">Nilai</th>
                        <th class="text-right hidden sm:table-cell">Dibayar</th>
                        <th class="text-right">Sisa</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td class="whitespace-nowrap font-mono">{{ $bill->bill_number }}</td>
                            <td class="whitespace-nowrap">{{ $bill->due_date->translatedFormat('d M Y') }}</td>
                            <td class="hidden md:table-cell">{{ $bill->party->name ?? '-' }}</td>
                            <td class="hidden lg:table-cell">{{ $bill->project->name ?? '-' }}</td>
                            <td class="text-right">Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                            <td class="text-right hidden sm:table-cell">Rp{{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                            <td class="text-right font-semibold {{ $bill->outstanding_amount > 0 ? 'text-[#B42318]' : 'text-[#166534]' }}">
                                @if($bill->outstanding_amount > 0)
                                    Rp{{ number_format($bill->outstanding_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($bill->status === 'paid')
                                    <span class="badge-success">Lunas</span>
                                @elseif($bill->status === 'partially_paid')
                                    <span class="badge-warning">Sebagian</span>
                                @elseif($bill->status === 'overdue')
                                    <span class="badge-danger">Terlambat</span>
                                @elseif($bill->status === 'archived')
                                    <span class="badge-info">Arsip</span>
                                @else
                                    <span class="badge-info">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('bills.show', $bill) }}" class="underline text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-[#171717]/70">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-t-2 border-[#171717] bg-[#F7F5EF]">
            <div class="table-responsive">{{ $bills->links() }}</div>
        </div>
    </div>
</x-app-layout>