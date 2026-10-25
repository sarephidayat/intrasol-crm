@extends('reports.pdf.layout')

@section('title', 'Ringkasan Bulanan')

@section('report-title', 'Ringkasan Pemasukan dan Pengeluaran Bulanan')

@section('meta')
    <span>Periode: {{ \Carbon\Carbon::create($year, $month, 1)->locale('id')->translatedFormat('F Y') }}</span>
    <span>Dibuat: {{ now()->locale('id')->translatedFormat('d F Y H:i') }} WIB</span>
@endsection

@section('content')
    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Pemasukan</div>
                <div class="value positive">Rp{{ number_format($income, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Total Pengeluaran</div>
                <div class="value negative">Rp{{ number_format($expense, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Arus Kas Bersih</div>
                <div class="value {{ $net < 0 ? 'negative' : 'positive' }}">
                    {{ $net < 0 ? '-' : '' }}Rp{{ number_format(abs($net), 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Transaksi Bulan Ini</div>

    @if ($transactions->isEmpty())
        <div class="empty">Tidak ada transaksi kas pada bulan ini.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th style="width: 62px;">Tanggal</th>
                    <th>Keterangan</th>
                    <th style="width: 110px;">Kategori</th>
                    <th style="width: 72px;">Jenis</th>
                    <th style="width: 100px;" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
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
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">Saldo bersih bulan ini</td>
                    <td class="text-right {{ $net < 0 ? 'negative' : 'positive' }}">
                        {{ $net < 0 ? '-' : '' }}Rp{{ number_format(abs($net), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="note">Hanya transaksi kas terealisasi yang ditampilkan. Transaksi diarsipkan tidak diikutkan.</div>
    @endif
@endsection
