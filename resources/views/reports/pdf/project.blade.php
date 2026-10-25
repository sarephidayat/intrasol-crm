@extends('reports.pdf.layout')

@section('title', 'Laporan Keuangan per Proyek')

@section('report-title', 'Laporan Keuangan per Proyek')

@section('meta')
    <span>Periode: {{ \Carbon\Carbon::parse($from)->locale('id')->translatedFormat('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->locale('id')->translatedFormat('d F Y') }}</span>
    <span>Dibuat: {{ now()->locale('id')->translatedFormat('d F Y H:i') }} WIB</span>
@endsection

@section('content')
    @if ($groups->isEmpty())
        <div class="empty">Tidak ada transaksi kas yang terhubung ke proyek pada periode ini.</div>
    @else
        <table class="summary">
            <tr>
                <td>
                    <div class="label">Total Pemasukan Proyek</div>
                    <div class="value positive">Rp{{ number_format($groups->sum('income'), 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Pengeluaran Proyek</div>
                    <div class="value negative">Rp{{ number_format($groups->sum('expense'), 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Arus Kas Bersih Proyek</div>
                    <div class="value {{ $groups->sum('income') - $groups->sum('expense') < 0 ? 'negative' : 'positive' }}">
                        Rp{{ number_format(abs($groups->sum('income') - $groups->sum('expense')), 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Ringkasan per Proyek</div>
        <table class="data avoid-break">
            <thead>
                <tr>
                    <th>Proyek</th>
                    <th style="width: 70px;" class="text-center">Transaksi</th>
                    <th class="text-right">Pemasukan</th>
                    <th class="text-right">Pengeluaran</th>
                    <th class="text-right">Arus Kas Bersih</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groups as $group)
                    <tr>
                        <td>{{ $group['project']?->name ?? '-' }}</td>
                        <td class="text-center">{{ $group['count'] }}</td>
                        <td class="text-right">Rp{{ number_format($group['income'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp{{ number_format($group['expense'], 0, ',', '.') }}</td>
                        <td class="text-right {{ $group['income'] - $group['expense'] < 0 ? 'negative' : 'positive' }}">
                            Rp{{ number_format(abs($group['income'] - $group['expense']), 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-right">Rp{{ number_format($groups->sum('income'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($groups->sum('expense'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format(abs($groups->sum('income') - $groups->sum('expense')), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="section-title">Detail Transaksi Proyek</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th style="width: 62px;">Tanggal</th>
                    <th style="width: 95px;">Proyek</th>
                    <th>Keterangan</th>
                    <th style="width: 95px;">Kategori</th>
                    <th style="width: 62px;">Jenis</th>
                    <th style="width: 92px;" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                        <td>{{ $transaction->project?->name ?? '-' }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ $transaction->category?->name ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $transaction->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                                {{ $transaction->type === 'income' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="text-right {{ $transaction->type === 'income' ? 'positive' : 'negative' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="note">Nilai di atas adalah arus kas terealisasi yang diatribusikan ke proyek, bukan nilai kontrak proyek.</div>
    @endif
@endsection
