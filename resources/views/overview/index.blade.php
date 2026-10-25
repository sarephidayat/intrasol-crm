<x-app-layout>
    @section('page-title', 'Dashboard Kas')
    @section('page-subtitle', 'Periode ' . now()->locale('id')->translatedFormat('F Y'))

    <div class="page-header flex flex-wrap justify-end gap-2">
        <a href="{{ route('transactions.create') }}" class="btn-primary text-sm">+ Transaksi Baru</a>
        <a href="{{ route('parties.index') }}" class="btn-secondary text-sm">Kelola Pihak</a>
        <a href="{{ route('projects.index') }}" class="btn-secondary text-sm">Kelola Proyek</a>
        <a href="{{ route('reports.index') }}" class="btn-secondary text-sm">Lihat Laporan</a>
    </div>

    {{-- Stats Cards - 2 columns on mobile, 4 on lg (equal width) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Saldo Kas Saat Ini</div>
            <div class="mt-1 font-['Space_Grotesk'] text-xl sm:text-2xl font-semibold">Rp{{ number_format($balance ?? 0, 0, ',', '.') }}</div>
            <div class="mt-1 text-[10px] text-[#171717]/60">Hanya transaksi kas terealisasi</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Pemasukan Bulan Ini</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold text-[#166534]">Rp{{ number_format($month['income'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Pengeluaran Bulan Ini</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold text-[#B42318]">Rp{{ number_format($month['expense'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Arus Kas Bersih</div>
            @php($net = $month['net'] ?? 0)
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold {{ $net < 0 ? 'text-[#B42318]' : 'text-[#166534]' }}">
                {{ $net < 0 ? '-' : '' }}Rp{{ number_format(abs($net), 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Piutang Belum Lunas</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold">Rp{{ number_format($receivables ?? 0, 0, ',', '.') }}</div>
            <div class="mt-1 text-[10px]"><a href="{{ route('bills.index', ['type' => 'receivable']) }}" class="underline">Lihat piutang</a></div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Utang Belum Lunas</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold">Rp{{ number_format($payables ?? 0, 0, ',', '.') }}</div>
            <div class="mt-1 text-[10px]"><a href="{{ route('bills.index', ['type' => 'payable']) }}" class="underline">Lihat utang</a></div>
        </div>
    </div>

    {{-- Charts - stacked on mobile, side-by-side on lg --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
        <div class="card h-full flex flex-col">
            <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <span>Pemasukan vs Pengeluaran per Bulan ({{ $year }})</span>
                <a href="{{ route('reports.index') }}" class="text-sm font-normal underline self-start sm:self-auto">Detail laporan</a>
            </div>
            <div class="card-body flex-1 min-h-[250px]">
                <x-monthly-chart :monthly="$monthly" :year="$year" />
            </div>
        </div>
        <div class="card h-full flex flex-col">
            <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <span>Pengeluaran Berdasarkan Kategori ({{ $year }})</span>
                <span class="text-xs font-normal text-[#171717]/70 self-start sm:self-auto">Hanya transaksi aktif</span>
            </div>
            <div class="card-body flex-1 min-h-[250px]">
                <x-expense-category-chart :items="$expenseByCategory['items'] ?? []" :total="$expenseByCategory['total'] ?? 0" :year="$year" />
            </div>
        </div>
    </div>

    {{-- Alert Cards - 3 columns on mobile --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Tagihan Terlambat</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold {{ $overdueBills->isEmpty() ? '' : 'text-[#B42318]' }}">
                {{ $overdueBills->count() }} tagihan
            </div>
            <div class="mt-1 text-sm">Rp{{ number_format($overdueBills->sum('outstanding_amount'), 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Jatuh Tempo 7 Hari</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold">{{ $dueSoonBills->count() }} tagihan</div>
            <div class="mt-1 text-sm">Rp{{ number_format($dueSoonBills->sum('outstanding_amount'), 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Rekonsiliasi Terakhir</div>
            @if ($latestReconciliation)
                <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold">
                    {{ $latestReconciliation->reconciliation_date->translatedFormat('d M Y') }}
                </div>
                <div class="mt-1 text-sm">
                    Status: {{ ucfirst($latestReconciliation->status) }} ·
                    <span class="{{ $latestReconciliation->difference === 0 ? 'text-[#166534]' : 'text-[#B42318]' }}">
                        Selisih Rp{{ number_format(abs($latestReconciliation->difference), 0, ',', '.') }}
                    </span>
                </div>
            @else
                <div class="mt-1 text-sm text-[#171717]/70">Belum ada rekonsiliasi.</div>
                <div class="mt-1 text-[10px]"><a href="{{ route('reconciliations.index') }}" class="underline">Buka rekonsiliasi</a></div>
            @endif
        </div>
    </div>

    {{-- Overdue/Due Soon Bills Table --}}
    @if ($overdueBills->isNotEmpty() || $dueSoonBills->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">Tagihan Perlu Perhatian</div>
            <div class="table-responsive">
                <table class="data-table min-w-[600px]">
                    <thead>
                        <tr>
                            <th>Jatuh Tempo</th>
                            <th>Jenis</th>
                            <th>Pihak</th>
                            <th>Keterangan</th>
                            <th class="text-right">Sisa</th>
                            <th class="text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overdueBills->concat($dueSoonBills)->sortBy('due_date') as $bill)
                            <tr>
                                <td class="whitespace-nowrap">{{ $bill->due_date->translatedFormat('d M Y') }}</td>
                                <td>{{ $bill->type === 'receivable' ? 'Piutang' : 'Utang' }}</td>
                                <td>{{ $bill->party?->name ?? '-' }}</td>
                                <td class="max-w-[150px] truncate">{{ \Illuminate\Support\Str::limit($bill->description, 48) }}</td>
                                <td class="text-right whitespace-nowrap">Rp{{ number_format($bill->outstanding_amount, 0, ',', '.') }}</td>
                                <td class="text-right">
                                    @if ($bill->due_date->lt(today()))
                                        <span class="badge-danger">Terlambat</span>
                                    @else
                                        <span class="badge-warning">Segera</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body border-t-2 border-[#171717] bg-[#F7F5EF] text-center">
                <a href="{{ route('bills.index') }}" class="underline text-sm">Kelola semua tagihan</a>
            </div>
        </div>
    @endif

    {{-- Recent Transactions Table --}}
    <div class="card">
        <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <span>Transaksi Terbaru</span>
            <a href="{{ route('transactions.index') }}" class="text-sm font-normal underline self-start sm:self-auto">Lihat semua</a>
        </div>
        <div class="table-responsive">
            <table class="data-table min-w-[680px]">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Kategori</th>
                        <th class="hidden sm:table-cell">Jenis</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right hidden sm:table-cell"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent ?? [] as $t)
                        <tr>
                            <td class="whitespace-nowrap">{{ $t->transaction_date->translatedFormat('d M Y') }}</td>
                            <td class="max-w-[200px] truncate">
                                <a href="{{ route('transactions.show', $t) }}" class="underline decoration-1 underline-offset-2 hover:no-underline">
                                    {{ \Illuminate\Support\Str::limit($t->description, 48) }}
                                </a>
                            </td>
                            <td>{{ $t->category?->name ?? '-' }}</td>
                            <td class="hidden sm:table-cell">
                                @if ($t->type === 'income')
                                    <span class="inline-block border-2 border-[#171717] bg-[#B9F4C8] px-2 py-0.5 text-[10px] text-[#166534]">Pemasukan</span>
                                @else
                                    <span class="inline-block border-2 border-[#171717] bg-[#B42318]/10 px-2 py-0.5 text-[10px] text-[#B42318]">Pengeluaran</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap {{ $t->type === 'income' ? 'text-[#166534]' : 'text-[#B42318]' }}">
                                {{ $t->type === 'income' ? '+' : '-' }}Rp{{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-right hidden sm:table-cell">
                                <a href="{{ route('transactions.show', $t) }}" class="underline text-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-[#171717]/70">Belum ada transaksi kas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>