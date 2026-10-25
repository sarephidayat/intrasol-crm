<x-app-layout>
    @section('page-title', 'Pihak (Klien/Vendor)')
    @section('page-subtitle', 'Master data pihak')

    <div class="flex justify-between items-center mb-4">
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ request('q') }}" placeholder="Cari nama..." class="border-2 border-[#171717] px-3 py-1">
            <select name="role" class="border-2 border-[#171717] px-3 py-1">
                <option value="">Semua peran</option>
                <option value="client" @selected(request('role')==='client')>Klien</option>
                <option value="vendor" @selected(request('role')==='vendor')>Vendor</option>
                <option value="both" @selected(request('role')==='both')>Keduanya</option>
            </select>
            <button class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Filter</button>
        </form>
        <a href="{{ route('parties.create') }}" class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">+ Tambah Pihak</a>
    </div>

    <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717] overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-[#171717]">
                    <th class="text-left px-4 py-2">Nama</th>
                    <th class="text-left px-4 py-2">Peran</th>
                    <th class="text-left px-4 py-2">PIC</th>
                    <th class="text-left px-4 py-2">Kontak</th>
                    <th class="text-left px-4 py-2">Status</th>
                    <th class="text-right px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parties as $p)
                    <tr class="border-b border-[#DDDDDD]">
                        <td class="px-4 py-2"><a href="{{ route('parties.show', $p) }}" class="underline">{{ $p->name }}</a></td>
                        <td class="px-4 py-2">{{ $p->role }}</td>
                        <td class="px-4 py-2">{{ $p->primary_pic_name }}</td>
                        <td class="px-4 py-2">{{ $p->primary_pic_email }} {{ $p->primary_pic_phone }}</td>
                        <td class="px-4 py-2">{{ $p->is_active ? 'Aktif' : 'Arsip' }}</td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('parties.edit', $p) }}" class="underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-[#171717]/70">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $parties->links() }}</div>
</x-app-layout>
