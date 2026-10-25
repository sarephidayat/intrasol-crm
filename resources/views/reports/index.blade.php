<x-app-layout>
    @section('page-title', 'Laporan')
    @section('page-subtitle', 'Laporan keuangan dan ekspor PDF')

    <div class="page-header">
        <form method="GET" action="{{ route('reports.index') }}" class="filter-form flex flex-col sm:flex-row flex-wrap gap-2 w-full sm:w-auto">
            <div class="flex-1 min-w-[140px]">
                <label for="from" class="block text-xs text-[#171717]/70 mb-1">Dari tanggal</label>
                <input id="from" type="date" name="from" value="{{ $from }}" class="form-input">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label for="to" class="block text-xs text-[#171717]/70 mb-1">Sampai tanggal</label>
                <input id="to" type="date" name="to" value="{{ $to }}" class="form-input">
            </div>
            <button class="btn-secondary whitespace-nowrap">Terapkan</button>
            <button type="submit" formaction="{{ route('reports.export.period') }}" class="btn-primary whitespace-nowrap">Ekspor PDF Transaksi</button>
        </form>
    </div>

    {{-- Summary Cards - 2 on mobile, 4 on md --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Pemasukan (Periode)</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold text-[#166534]">Rp{{ number_format($income, 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Pengeluaran (Periode)</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold text-[#B42318]">Rp{{ number_format($expense, 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Piutang Belum Lunas</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold">Rp{{ number_format($receivables, 0, ',', '.') }}</div>
        </div>
        <div class="card card-body">
            <div class="text-xs text-[#171717]/70">Utang Belum Lunas</div>
            <div class="mt-1 font-['Space_Grotesk'] text-lg sm:text-xl font-semibold">Rp{{ number_format($payables, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Export Forms - stacked on mobile --}}
    <div class="card mb-4">
        <div class="card-header">Ekspor PDF</div>
        <div class="card-body space-y-3">
            {{-- Monthly --}}
            <form method="GET" action="{{ route('reports.export.monthly') }}" class="card p-3 space-y-2">
                <div class="text-sm font-semibold">Ringkasan Bulanan</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <select name="month" class="form-select">
                        @foreach ([1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $monthNumber => $monthLabel)
                            <option value="{{ $monthNumber }}" @selected($monthNumber === (int) now()->month)>{{ $monthLabel }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="form-select">
                        @for ($y = (int) now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    <button class="btn-primary" type="submit">Ekspor PDF</button>
                </div>
            </form>

            {{-- Annual --}}
            <form method="GET" action="{{ route('reports.export.annual') }}" class="card p-3 space-y-2">
                <div class="text-sm font-semibold">Ringkasan Kas Tahunan</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <select name="year" class="form-select">
                        @for ($y = (int) now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    <button class="btn-primary" type="submit">Ekspor PDF</button>
                </div>
            </form>

            {{-- Project --}}
            <form method="GET" action="{{ route('reports.export.project') }}" class="card p-3 space-y-2">
                <div class="text-sm font-semibold">Laporan per Proyek</div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                    <input type="date" name="from" value="{{ $from }}" class="form-input">
                    <input type="date" name="to" value="{{ $to }}" class="form-input">
                    <select name="project_id" class="form-select">
                        <option value="">Semua proyek</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary" type="submit">Ekspor PDF</button>
                </div>
            </form>

            {{-- Bills --}}
            <div class="card p-3 space-y-2">
                <div class="text-sm font-semibold">Laporan Piutang / Utang</div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ route('reports.export.bills', ['type' => 'receivable']) }}" class="btn-primary text-center">Ekspor Piutang</a>
                    <a href="{{ route('reports.export.bills', ['type' => 'payable']) }}" class="btn-secondary text-center">Ekspor Utang</a>
                </div>
            </div>

            {{-- Reconciliation --}}
            <div class="card p-3 space-y-2">
                <div class="text-sm font-semibold">Laporan Rekonsiliasi</div>
                <a href="{{ route('reports.export.reconciliation') }}" class="btn-primary inline-block text-center">Ekspor Rekonsiliasi Terakhir</a>
            </div>

            <div class="border-2 border-dashed border-[#171717]/40 p-3 text-[10px] text-[#171717]/70">
                Semua PDF menggunakan ukuran A4. Laporan transaksi memakai orientasi landscape; ringkasan memakai portrait.
                Hanya transaksi kas terealisasi yang dihitung; transaksi diarsipkan tidak diikutkan.
            </div>
        </div>
    </div>

    {{-- Monthly Chart & Table --}}
    <div class="card">
        <div class="card-header">Pemasukan vs Pengeluaran per Bulan ({{ now()->year }})</div>
        <div class="card-body min-h-[250px]">
            <x-monthly-chart :monthly="$byMonth" :year="now()->year" />
        </div>
        <div class="table-responsive border-t-2 border-[#171717]">
            <table class="data-table min-w-[480px]">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th class="text-right">Pemasukan</th>
                        <th class="text-right">Pengeluaran</th>
                        <th class="text-right">Net</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([1,2,3,4,5,6,7,8,9,10,11,12] as $m)
                        <tr>
                            <td class="whitespace-nowrap">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</td>
                            <td class="text-right">Rp{{ number_format($byMonth[$m]['income'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp{{ number_format($byMonth[$m]['expense'], 0, ',', '.') }}</td>
                            <td class="text-right {{ $byMonth[$m]['net'] < 0 ? 'text-[#B42318]' : 'text-[#166534]' }}">
                                {{ $byMonth[$m]['net'] < 0 ? '-' : '' }}Rp{{ number_format(abs($byMonth[$m]['net']), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>