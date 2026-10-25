<x-app-layout>
    @section('page-title', 'Kategori')
    @section('page-subtitle', 'Kategori pemasukan & pengeluaran')

    <div class="flex justify-between items-center mb-4">
        <div class="flex gap-2">
            <a href="{{ route('categories.index', ['type'=>'income']) }}" class="border-2 border-[#171717] px-3 py-1 {{ $type==='income' ? 'bg-[#F5E64D]' : 'bg-white' }} shadow-[2px_2px_0_#171717]">Pemasukan</a>
            <a href="{{ route('categories.index', ['type'=>'expense']) }}" class="border-2 border-[#171717] px-3 py-1 {{ $type==='expense' ? 'bg-[#F5E64D]' : 'bg-white' }} shadow-[2px_2px_0_#171717]">Pengeluaran</a>
        </div>
        <a href="{{ route('categories.create', ['type'=>$type]) }}" class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">+ Tambah Kategori</a>
    </div>

    <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717] overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-[#171717]">
                    <th class="text-left px-4 py-2">Nama</th>
                    <th class="text-left px-4 py-2">Deskripsi</th>
                    <th class="text-left px-4 py-2">Status</th>
                    <th class="text-right px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $c)
                    <tr class="border-b border-[#DDDDDD]">
                        <td class="px-4 py-2">{{ $c->name }}</td>
                        <td class="px-4 py-2">{{ $c->description }}</td>
                        <td class="px-4 py-2">{{ $c->is_active ? 'Aktif' : 'Arsip' }}</td>
                        <td class="px-4 py-2 text-right"><a href="{{ route('categories.edit', $c) }}" class="underline">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-[#171717]/70">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
</x-app-layout>
