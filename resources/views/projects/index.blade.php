<x-app-layout>
    @section('page-title', 'Proyek')
    @section('page-subtitle', 'Daftar proyek')

    <div class="page-header">
        <form method="GET" class="filter-form flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <input name="q" value="{{ request('q') }}" placeholder="Cari kode/nama..." class="form-input flex-1 min-w-[200px]">
            <button class="btn-secondary whitespace-nowrap">Filter</button>
        </form>
        <a href="{{ route('projects.create') }}" class="btn-primary whitespace-nowrap">+ Tambah Proyek</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table min-w-[640px]">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th class="hidden md:table-cell">Klien</th>
                        <th>Status</th>
                        <th class="text-right hidden sm:table-cell">Pemasukan</th>
                        <th class="text-right hidden sm:table-cell">Pengeluaran</th>
                        <th class="text-right hidden sm:table-cell">Piutang</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $pr)
                        @php($prReceivable = $receivables[$pr->id] ?? 0)
                        <tr>
                            <td class="whitespace-nowrap font-mono">{{ $pr->code }}</td>
                            <td class="max-w-[200px] truncate">
                                <a href="{{ route('projects.show', $pr) }}" class="underline hover:no-underline">{{ $pr->name }}</a>
                            </td>
                            <td class="hidden md:table-cell">{{ $pr->party->name ?? '-' }}</td>
                            <td>
                                @if($pr->is_active)
                                    <span class="badge-success">Aktif</span>
                                @else
                                    <span class="badge-info">Arsip</span>
                                @endif
                            </td>
                            <td class="text-right hidden sm:table-cell">Rp{{ number_format($pr->income_sum ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right hidden sm:table-cell">Rp{{ number_format($pr->expense_sum ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right hidden sm:table-cell {{ $prReceivable > 0 ? 'text-[#F5E64D] font-semibold' : '' }}">
                                @if($prReceivable > 0)
                                    Rp{{ number_format($prReceivable, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('projects.edit', $pr) }}" class="underline text-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-[#171717]/70">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-t-2 border-[#171717] bg-[#F7F5EF]">
            <div class="table-responsive">{{ $projects->links() }}</div>
        </div>
    </div>
</x-app-layout>