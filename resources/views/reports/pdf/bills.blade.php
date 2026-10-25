@extends('reports.pdf.layout')

@section('title', $type === 'receivable' ? 'Laporan Piutang' : 'Laporan Utang')

@section('report-title', $type === 'receivable' ? 'Laporan Piutang' : 'Laporan Utang')

@section('meta')
    <span>Jenis: {{ $type === 'receivable' ? 'Piutang (Receivable)' : 'Utang (Payable)' }}</span>
    <span>Dibuat: {{ now()->locale('id')->translatedFormat('d F Y H:i') }} WIB</span>
@endsection

@section('content')
    <table class="summary">
        <tr>
            <td>
                <div class="label">Jumlah Dokumen</div>
                <div class="value">{{ $bills->count() }}</div>
            </td>
            <td>
                <div class="label">Total Nilai</div>
                <div class="value">Rp{{ number_format($bills->sum('amount'), 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Sisa Belum Lunas</div>
                <div class="value {{ $totalOutstanding > 0 ? 'negative' : 'positive' }}">
                    Rp{{ number_format($totalOutstanding, 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    @php
        $statusLabels = [
            'unpaid' => 'Belum Bayar',
            'partial' => 'Sebagian',
            'paid' => 'Lunas',
            'overdue' => 'Terlambat',
        ];
    @endphp

    @if ($bills->isEmpty())
        <div class="empty">Tidak ada data {{ $type === 'receivable' ? 'piutang' : 'utang' }}.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 26px;">No</th>
                    <th style="width: 58px;">Jatuh Tempo</th>
                    <th style="width: 68px;">Nomor</th>
                    <th style="width: 82px;">Pihak</th>
                    <th>Keterangan</th>
                    <th style="width: 72px;" class="text-right">Nilai</th>
                    <th style="width: 66px;" class="text-right">Dibayar</th>
                    <th style="width: 66px;" class="text-right">Sisa</th>
                    <th style="width: 58px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $index => $bill)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $bill->due_date?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $bill->bill_number ?? '-' }}</td>
                        <td>{{ $bill->party?->name ?? '-' }}</td>
                        <td>{{ $bill->description }}</td>
                        <td class="text-right">Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                        <td class="text-right">Rp{{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                        <td class="text-right">Rp{{ number_format($bill->outstanding_amount, 0, ',', '.') }}</td>
                        <td>{{ $statusLabels[$bill->status] ?? ucfirst($bill->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">Total</td>
                    <td class="text-right">Rp{{ number_format($bills->sum('amount'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($bills->sum('paid_amount'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($totalOutstanding, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <div class="note">
            Piutang/utang yang belum lunas tidak memengaruhi saldo kas. Nilai sisa dihitung dari pembayaran yang berhasil.
        </div>
    @endif
@endsection
