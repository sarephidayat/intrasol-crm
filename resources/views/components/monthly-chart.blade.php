@props(['monthly', 'year' => null])

<div {{ $attributes->merge(['class' => '']) }}>
    @php
        $year ??= (int) now()->year;
        $months = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $max = 0;
        foreach ($monthly as $values) {
            $max = max($max, $values['income'], $values['expense']);
        }
        $rupiah = fn (int $value) => 'Rp' . number_format($value, 0, ',', '.');
    @endphp

    @if ($max === 0)
        <div class="px-4 py-10 text-center text-sm text-[#171717]/70">
            Belum ada transaksi kas terealisasi pada tahun {{ $year }}.
        </div>
    @else
        <div class="px-4 pt-4">
            <div class="flex items-center justify-between gap-2 text-xs text-[#171717]/70">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1">
                        <span class="inline-block h-3 w-3 border border-[#171717] bg-[#166534]"></span> Pemasukan
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="inline-block h-3 w-3 border border-[#171717] bg-[#B42318]"></span> Pengeluaran
                    </span>
                </div>
                <span>Nilai tertinggi: {{ $rupiah($max) }}</span>
            </div>

            <div class="mt-4 flex items-end gap-1 sm:gap-2 h-44 overflow-x-auto pb-1" role="img"
                 aria-label="Grafik pemasukan dan pengeluaran kas per bulan tahun {{ $year }}">
                @foreach ($monthly as $m => $values)
                    @php
                        $incomeHeight = $max > 0 ? round($values['income'] / $max * 100, 2) : 0;
                        $expenseHeight = $max > 0 ? round($values['expense'] / $max * 100, 2) : 0;
                    @endphp
                    <div class="flex-1 min-w-[26px] flex flex-col items-center justify-end gap-1">
                        <div class="flex items-end justify-center gap-[2px] w-full h-40">
                            @if ($values['income'] > 0)
                                <div class="w-2 sm:w-3 border border-[#171717] bg-[#166534]"
                                     style="height: {{ $incomeHeight }}%"
                                     title="{{ $months[$m] }} {{ $year }} pemasukan: {{ $rupiah($values['income']) }}"></div>
                            @endif
                            @if ($values['expense'] > 0)
                                <div class="w-2 sm:w-3 border border-[#171717] bg-[#B42318]"
                                     style="height: {{ $expenseHeight }}%"
                                     title="{{ $months[$m] }} {{ $year }} pengeluaran: {{ $rupiah($values['expense']) }}"></div>
                            @endif
                        </div>
                        <span class="text-[10px] sm:text-xs {{ $m === (int) now()->month && (int) $year === (int) now()->year ? 'font-bold' : '' }}">{{ $months[$m] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <table class="sr-only">
            <caption>Pemasukan dan pengeluaran kas per bulan tahun {{ $year }}</caption>
            <thead>
                <tr><th>Bulan</th><th>Pemasukan</th><th>Pengeluaran</th><th>Net</th></tr>
            </thead>
            <tbody>
                @foreach ($monthly as $m => $values)
                    <tr>
                        <th scope="row">{{ $months[$m] }}</th>
                        <td>{{ $rupiah($values['income']) }}</td>
                        <td>{{ $rupiah($values['expense']) }}</td>
                        <td>{{ $rupiah($values['net']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
