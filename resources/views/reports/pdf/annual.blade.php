@extends('reports.pdf.layout')

@section('title', 'Laporan Kas Tahunan')

@section('report-title', 'Ringkasan Kas Tahunan')

@section('meta')
    <span>Tahun: {{ $year }}</span>
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

    @php
        $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $hasData = collect($monthly)->contains(fn ($row) => $row['income'] > 0 || $row['expense'] > 0);
    @endphp

    @if (! $hasData)
        <div class="empty">Belum ada transaksi kas terealisasi pada tahun {{ $year }}.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 100px;">Bulan</th>
                    <th class="text-right">Pemasukan</th>
                    <th class="text-right">Pengeluaran</th>
                    <th class="text-right">Arus Kas Bersih</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthly as $monthNumber => $row)
                    <tr>
                        <td>{{ $months[$monthNumber] }}</td>
                        <td class="text-right">Rp{{ number_format($row['income'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp{{ number_format($row['expense'], 0, ',', '.') }}</td>
                        <td class="text-right {{ $row['net'] < 0 ? 'negative' : 'positive' }}">
                            {{ $row['net'] < 0 ? '-' : '' }}Rp{{ number_format(abs($row['net']), 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="text-right">Rp{{ number_format($income, 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($expense, 0, ',', '.') }}</td>
                    <td class="text-right {{ $net < 0 ? 'negative' : 'positive' }}">
                        {{ $net < 0 ? '-' : '' }}Rp{{ number_format(abs($net), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="note">Hanya transaksi kas terealisasi yang dihitung. Transaksi diarsipkan tidak memengaruhi saldo.</div>
    @endif
@endsection
