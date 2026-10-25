@extends('reports.pdf.layout')

@section('title', 'Laporan Rekonsiliasi')

@section('report-title', 'Laporan Rekonsiliasi Bank')

@section('meta')
    <span>Tanggal: {{ $reconciliation->reconciliation_date->locale('id')->translatedFormat('d F Y') }}</span>
    <span>Dibuat: {{ now()->locale('id')->translatedFormat('d F Y H:i') }} WIB</span>
@endsection

@section('content')
    @php
        $difference = (int) $reconciliation->difference;
        $statusLabels = ['draft' => 'Draft', 'completed' => 'Selesai', 'reopened' => 'Dibuka Ulang'];
    @endphp

    <table class="summary">
        <tr>
            <td>
                <div class="label">Saldo Sistem</div>
                <div class="value">Rp{{ number_format($reconciliation->system_balance, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Saldo Aktual Bank</div>
                <div class="value">Rp{{ number_format($reconciliation->actual_bank_balance, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Selisih</div>
                <div class="value {{ $difference === 0 ? 'positive' : 'negative' }}">
                    {{ $difference < 0 ? '-' : '' }}Rp{{ number_format(abs($difference), 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="data avoid-break" style="margin-bottom: 12px;">
        <tbody>
            <tr>
                <th style="width: 150px;">Status Rekonsiliasi</th>
                <td>{{ $statusLabels[$reconciliation->status] ?? ucfirst($reconciliation->status) }}</td>
            </tr>
            <tr>
                <th>Diselesaikan Pada</th>
                <td>{{ $reconciliation->completed_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Rekonsiliasi Oleh</th>
                <td>{{ $reconciliation->creator?->name ?? '-' }}</td>
            </tr>
            @if ($reconciliation->bankStatement)
                <tr>
                    <th>Nama Rekening</th>
                    <td>{{ $reconciliation->bankStatement->account_name }}</td>
                </tr>
                <tr>
                    <th>Periode Rekening</th>
                    <td>
                        {{ $reconciliation->bankStatement->period_start?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                        &ndash;
                        {{ $reconciliation->bankStatement->period_end?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                    </td>
                </tr>
            @endif
            <tr>
                <th>Catatan</th>
                <td>{{ $reconciliation->notes ?: '-' }}</td>
            </tr>
        </tbody>
    </table>

    @php
        $entries = $reconciliation->bankStatement?->entries ?? collect();
    @endphp

    <div class="section-title">Rincian Mutasi Bank</div>

    @if ($entries->isEmpty())
        <div class="empty">Tidak ada rincian mutasi bank yang tercatat pada rekonsiliasi ini.</div>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 26px;">No</th>
                    <th style="width: 62px;">Tanggal</th>
                    <th>Keterangan</th>
                    <th style="width: 80px;">Referensi</th>
                    <th style="width: 62px;">Jenis</th>
                    <th style="width: 90px;" class="text-right">Jumlah</th>
                    <th style="width: 70px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $index => $entry)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $entry->entry_date?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>{{ $entry->reference ?? '-' }}</td>
                        <td>{{ $entry->type === 'credit' ? 'Masuk' : 'Keluar' }}</td>
                        <td class="text-right">Rp{{ number_format($entry->amount, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($entry->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="note">Selisih rekonsiliasi tidak otomatis dicatat sebagai pemasukan atau pengeluaran kas.</div>
    @endif
@endsection
