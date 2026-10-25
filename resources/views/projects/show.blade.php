<x-app-layout>
    @section('page-title', 'Detail Proyek')
    @section('page-subtitle', $project->name)

    <div class="page-header">
        <a href="{{ route('projects.index') }}" class="btn-secondary whitespace-nowrap">← Kembali ke Daftar Proyek</a>
    </div>

    {{-- Project Info Card --}}
    <div class="card mb-4">
        <div class="card-header">Informasi Proyek</div>
        <div class="card-body space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">Kode Proyek</label>
                    <div class="font-mono text-base">{{ $project->code }}</div>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">Status</label>
                    <div>
                        @if($project->is_active)
                            <span class="badge-success">Aktif</span>
                        @else
                            <span class="badge-info">Arsip</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">Klien / Pihak</label>
                    <div>{{ $project->party->name ?? '<span class="text-[#171717]/60">-</span>' }}</div>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">PIC Klien</label>
                    <div>{{ $project->party->primary_pic_name ?? '<span class="text-[#171717]/60">-</span>' }}</div>
                </div>
            </div>
            @if($project->description)
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">Deskripsi</label>
                    <div class="mt-1 whitespace-pre-wrap">{{ $project->description }}</div>
                </div>
            @endif
            <div class="pt-2 border-t border-[#DDDDDD]">
                <label class="text-xs uppercase tracking-wide text-[#171717]/60">Dibuat</label>
                <div>{{ $project->created_at->translatedFormat('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Financial Summary Cards - 2 columns on mobile, 4 on lg --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Total Pemasukan (Realized)</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold text-[#166534]">Rp{{ number_format($income ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Total Pengeluaran (Realized)</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold text-[#B42318]">Rp{{ number_format($expense ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Saldo Kas Proyek</div>
            @php($balance = ($income ?? 0) - ($expense ?? 0))
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold {{ $balance < 0 ? 'text-[#B42318]' : 'text-[#166534]' }}">
                {{ $balance < 0 ? '-' : '' }}Rp{{ number_format(abs($balance), 0, ',', '.') }}
            </div>
            <div class="mt-1 text-[10px] text-[#171717]/60">Berdasarkan transaksi terealisasi</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Piutang Outstanding</div>
            @php($receivables = $receivables ?? 0)
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold text-[#171717]">Rp{{ number_format($receivables, 0, ',', '.') }}</div>
            <div class="mt-1 text-[10px] text-[#171717]/60">Belum diterima dari klien</div>
        </div>
    </div>

    {{-- Expense Breakdown --}}
    <div class="card mb-4">
        <div class="card-header">Rincian Pengeluaran</div>
        <div class="card-body grid grid-cols-3 gap-3">
            <div class="card p-3">
                <div class="text-xs uppercase tracking-wide text-[#171717]/60">Pembayaran Developer</div>
                <div class="font-['Space_Grotesk'] text-lg font-semibold text-[#B42318] mt-1">Rp{{ number_format($devPayments, 0, ',', '.') }}</div>
            </div>
            <div class="card p-3">
                <div class="text-xs uppercase tracking-wide text-[#171717]/60">Bonus / Komisi Sales</div>
                <div class="font-['Space_Grotesk'] text-lg font-semibold text-[#B42318] mt-1">Rp{{ number_format($salesPayments, 0, ',', '.') }}</div>
            </div>
            <div class="card p-3">
                <div class="text-xs uppercase tracking-wide text-[#171717]/60">Pengeluaran Lainnya</div>
                <div class="font-['Space_Grotesk'] text-lg font-semibold text-[#B42318] mt-1">Rp{{ number_format($otherExpenses, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Piutang Detail --}}
    @php
        $projectBills = \App\Models\Bill::where('type', 'receivable')
            ->where('project_id', $project->id)
            ->whereNull('archived_at')
            ->with('party')
            ->get();
    @endphp
    @if($projectBills->count() > 0)
        <div class="card mb-4">
            <div class="card-header">Detail Piutang</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table min-w-[560px]">
                        <thead>
                            <tr>
                                <th>No. Tagihan</th>
                                <th class="hidden md:table-cell">Klien</th>
                                <th class="text-right">Nilai</th>
                                <th class="text-right hidden sm:table-cell">Dibayar</th>
                                <th class="text-right">Sisa</th>
                                <th>Status</th>
                                <th class="hidden sm:table-cell">Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projectBills as $bill)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $bill->bill_number }}</td>
                                    <td class="hidden md:table-cell">{{ $bill->party->name }}</td>
                                    <td class="text-right">Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                                    <td class="text-right hidden sm:table-cell">Rp{{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                                    <td class="text-right font-semibold">{{ $bill->outstanding_amount > 0 ? 'Rp' . number_format($bill->outstanding_amount, 0, ',', '.') : '-' }}</td>
                                    <td>
                                        @if($bill->status === 'paid')
                                            <span class="badge-success">Lunas</span>
                                        @elseif($bill->status === 'partially_paid')
                                            <span class="badge-warning">Sebagian</span>
                                        @elseif($bill->status === 'overdue')
                                            <span class="badge-danger">Terlambat</span>
                                        @else
                                            <span class="badge-info">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td class="hidden sm:table-cell whitespace-nowrap">{{ $bill->due_date->translatedFormat('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Transaction List --}}
    <div class="card">
        <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <span>Transaksi Proyek</span>
            <span class="text-xs text-[#171717]/70 self-start sm:self-auto">{{ $transactions->count() }} transaksi</span>
        </div>
        <div class="table-responsive">
            <table class="data-table min-w-[720px]">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th class="hidden md:table-cell">Kategori</th>
                        <th class="hidden lg:table-cell">Penerima</th>
                        <th>Jenis</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td class="whitespace-nowrap">{{ $tx->transaction_date->translatedFormat('d M Y') }}</td>
                            <td class="max-w-[200px] truncate">{{ \Illuminate\Support\Str::limit($tx->description, 60) }}</td>
                            <td class="hidden md:table-cell">{{ $tx->category->name ?? '<span class="text-[#171717]/60">-</span>' }}</td>
                            <td class="hidden lg:table-cell">{{ $tx->party->name ?? '<span class="text-[#171717]/60">-</span>' }}</td>
                            <td>
                                @if($tx->type === 'income')
                                    <span class="badge-success">Pemasukan</span>
                                @else
                                    <span class="badge-info">Pengeluaran</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap {{ $tx->type === 'income' ? 'text-[#166534]' : 'text-[#B42318]' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp{{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-[#171717]/70">Belum ada transaksi untuk proyek ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->count() > 0)
            <div class="card-body border-t-2 border-[#171717] bg-[#F7F5EF]">
                <div class="flex flex-wrap justify-between gap-2 text-sm">
                    <span>Total Pemasukan: <strong class="text-[#166534]">Rp{{ number_format($transactions->where('type','income')->sum('amount'), 0, ',', '.') }}</strong></span>
                    <span>Total Pengeluaran: <strong class="text-[#B42318]">Rp{{ number_format($transactions->where('type','expense')->sum('amount'), 0, ',', '.') }}</strong></span>
                    <span>Saldo: <strong class="{{ (($transactions->where('type','income')->sum('amount')) - ($transactions->where('type','expense')->sum('amount'))) < 0 ? 'text-[#B42318]' : 'text-[#166534]' }}">Rp{{ number_format(($transactions->where('type','income')->sum('amount')) - ($transactions->where('type','expense')->sum('amount')), 0, ',', '.') }}</strong></span>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>