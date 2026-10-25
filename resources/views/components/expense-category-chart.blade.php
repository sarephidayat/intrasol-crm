@props(['items' => [], 'total' => 0, 'year' => null])

@php
    $year ??= (int) now()->year;
    $rupiah = fn (int $value) => 'Rp' . number_format($value, 0, ',', '.');
    $conicGradient = '';
    if (! empty($items) && $total > 0) {
        $segments = [];
        $current = 0;
        $colors = ['#171717', '#B42318', '#F5E64D', '#166534', '#6B6B6B', '#9CA3AF', '#D4B942', '#22C55E'];
        foreach ($items as $index => $item) {
            $color = $colors[$index % count($colors)];
            $start = $current;
            $end = $current + $item['percent'];
            $segments[] = $color . ' ' . $start . '% ' . $end . '%';
            $current = $end;
        }
        $conicGradient = 'conic-gradient(' . implode(', ', $segments) . ')';
    }
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if ($total === 0 || empty($items))
        <div class="px-4 py-10 text-center text-sm text-[#171717]/70">
            Belum ada pengeluaran kas terealisasi pada tahun {{ $year }}.
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.1fr] gap-4 items-center px-4 py-4">
            <div class="flex items-center justify-center">
                <div class="relative h-48 w-48 sm:h-56 sm:w-56">
                    <div class="absolute inset-0 border-2 border-[#171717] rounded-full shadow-[4px_4px_0_#171717]" style="background: {{ $conicGradient }}"></div>
                    <div class="absolute inset-6 sm:inset-8 bg-white border-2 border-[#171717] rounded-full flex flex-col items-center justify-center text-center">
                        <div class="text-xs text-[#171717]/70">Total Pengeluaran</div>
                        <div class="mt-1 font-['Space_Grotesk'] text-sm sm:text-base font-semibold">{{ $rupiah($total) }}</div>
                        <div class="text-xs text-[#171717]/70">Tahun {{ $year }}</div>
                    </div>
                </div>
            </div>
            <div class="space-y-2">
                <div class="text-xs uppercase tracking-wide text-[#171717]/70">Distribusi Kategori</div>
                <div class="max-h-56 overflow-auto pr-1">
                    @foreach ($items as $index => $item)
                        @php
                            $colors = ['#171717', '#B42318', '#F5E64D', '#166534', '#6B6B6B', '#9CA3AF', '#D4B942', '#22C55E'];
                            $dot = $colors[$index % count($colors)];
                        @endphp
                        <div class="flex items-center justify-between gap-2 border-b border-[#DDDDDD] py-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="inline-block h-3 w-3 border border-[#171717]" style="background: {{ $dot }}"></span>
                                <span class="text-sm truncate">{{ $item['name'] }}</span>
                            </div>
                            <div class="text-sm text-right whitespace-nowrap">
                                <span>{{ $rupiah($item['amount']) }}</span>
                                <span class="text-[#171717]/70">({{ number_format($item['percent'], 1, ',', '.') }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <table class="sr-only">
            <caption>Pengeluaran berdasarkan kategori tahun {{ $year }}</caption>
            <thead>
                <tr><th>Kategori</th><th>Jumlah</th><th>Persentase</th></tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <th scope="row">{{ $item['name'] }}</th>
                        <td>{{ $rupiah($item['amount']) }}</td>
                        <td>{{ number_format($item['percent'], 1, ',', '.') }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
