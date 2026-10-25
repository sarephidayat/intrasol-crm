<x-app-layout>
    @section('page-title', 'Transaksi Kas')
    @section('page-subtitle', 'Pencatatan pemasukan & pengeluaran')

    <div class="page-header">
        <form method="GET" class="filter-form flex flex-col sm:flex-row flex-wrap gap-2 w-full sm:w-auto">
            <div class="flex-1 min-w-[140px]">
                <label class="sr-only" for="from">Dari tanggal</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}" placeholder="Dari" class="form-input">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="sr-only" for="to">Sampai tanggal</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}" placeholder="Sampai" class="form-input">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="sr-only" for="type">Jenis</label>
                <select name="type" id="type" class="form-select">
                    <option value="">Semua jenis</option>
                    <option value="income" @selected(request('type')==='income')>Pemasukan</option>
                    <option value="expense" @selected(request('type')==='expense')>Pengeluaran</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="sr-only" for="category_id">Kategori</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }} ({{ $c->type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="sr-only" for="q">Cari</label>
                <input name="q" id="q" value="{{ request('q') }}" placeholder="Cari keterangan..." class="form-input">
            </div>
            <button class="btn-secondary whitespace-nowrap">Filter</button>
        </form>
        <a href="{{ route('transactions.create') }}" class="btn-primary whitespace-nowrap">+ Tambah Transaksi</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="data-table min-w-[720px]">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="hidden md:table-cell">Kategori</th>
                        <th class="hidden lg:table-cell">Pihak/Proyek</th>
                        <th>Jenis</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right hidden sm:table-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td class="whitespace-nowrap">{{ $t->transaction_date->format('d/m/Y') }}</td>
                            <td class="max-w-[200px] truncate">
                                <a href="{{ route('transactions.show', $t) }}" class="underline hover:no-underline">
                                    {{ $t->description }}
                                </a>
                            </td>
                            <td class="hidden md:table-cell">{{ $t->category->name }}</td>
                            <td class="hidden lg:table-cell max-w-[150px] truncate">
                                {{ $t->party->name ?? '-' }} {{ $t->project ? '/ '.$t->project->name : '' }}
                            </td>
                            <td>
                                @if ($t->type === 'income')
                                    <span class="badge-success">Pemasukan</span>
                                @else
                                    <span class="badge-info">Pengeluaran</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap {{ $t->type === 'income' ? 'text-[#166534]' : 'text-[#B42318]' }}">
                                {{ $t->type === 'income' ? '+' : '-' }}Rp{{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-right hidden sm:table-cell">
                                <a href="{{ route('transactions.edit', $t) }}" class="underline text-sm">Koreksi</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-[#171717]/70">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-t-2 border-[#171717] bg-[#F7F5EF]">
            <div class="table-responsive">{{ $transactions->links() }}</div>
        </div>
    </div>
</x-app-layout>