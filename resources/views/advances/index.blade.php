<x-app-layout>
    @section('page-title', 'Dana Talangan')
    @section('page-subtitle', 'Piutang ke Pengurus')

    <div class="flex justify-between items-center mb-4">
        <form method="GET" class="flex gap-2">
            <select name="status" class="border-2 border-[#171717] px-3 py-1">
                <option value="">Semua status</option>
                <option value="open" @selected(request('status')==='open')>Belum lunas</option>
                <option value="reimbursed" @selected(request('status')==='reimbursed')>Lunas</option>
            </select>
            <button class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Filter</button>
        </form>
        <a href="{{ route('advances.create') }}" class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">+ Catat Talangan</a>
    </div>

    <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717] overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-[#171717]">
                    <th class="text-left px-4 py-2">No.</th>
                    <th class="text-left px-4 py-2">Penerima</th>
                    <th class="text-left px-4 py-2">Tanggal</th>
                    <th class="text-right px-4 py-2">Jumlah</th>
                    <th class="text-right px-4 py-2">Dikembalikan</th>
                    <th class="text-right px-4 py-2">Sisa</th>
                    <th class="text-left px-4 py-2">Status</th>
                    <th class="text-right px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advances as $a)
                    <tr class="border-b border-[#DDDDDD]">
                        <td class="px-4 py-2"><a href="{{ route('advances.show', $a) }}" class="underline">{{ $a->reference }}</a></td>
                        <td class="px-4 py-2">{{ $a->person_name }}</td>
                        <td class="px-4 py-2">{{ $a->advance_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-right">Rp{{ number_format($a->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-right">Rp{{ number_format($a->reimbursed_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-right">Rp{{ number_format($a->outstanding_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ $a->status }}</td>
                        <td class="px-4 py-2 text-right">
                            @if($a->outstanding_amount > 0)
                                <a href="{{ route('advances.reimburse.create', $a) }}" class="underline">Reimburse</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-6 text-center text-[#171717]/70">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $advances->links() }}</div>
</x-app-layout>
