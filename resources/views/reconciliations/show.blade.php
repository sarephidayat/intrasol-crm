<x-app-layout>
    @section('page-title', 'Detail Rekonsiliasi')
    @section('page-subtitle', 'Bank Jago')

    <div class="mb-4">
        <a href="{{ route('reconciliations.index') }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0_#171717] inline-block">← Kembali ke Daftar</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Reconciliation Info --}}
        <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 bg-[#F5E64D]">
                <h2 class="font-['Space_Grotesk'] text-lg font-semibold">Informasi Rekonsiliasi</h2>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">Tanggal Rekonsiliasi</label>
                    <div>{{ $reconciliation->reconciliation_date->translatedFormat('d M Y') }}</div>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">Status</label>
                    <div>
                        @if($reconciliation->status === 'completed')
                            <span class="inline-block border-2 border-[#171717] bg-[#B9F4C8] px-2 py-0.5 text-xs text-[#166534]">Selesai</span>
                        @elseif($reconciliation->status === 'draft')
                            <span class="inline-block border-2 border-[#171717] bg-[#F5E64D] px-2 py-0.5 text-xs">Draft</span>
                        @else
                            <span class="inline-block border-2 border-[#171717] bg-white px-2 py-0.5 text-xs">{{ $reconciliation->status }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">Dibuat Oleh</label>
                    <div>{{ $reconciliation->creator->name ?? 'System' }}</div>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-[#171717]/60">Dibuat Pada</label>
                    <div>{{ $reconciliation->created_at->translatedFormat('d M Y H:i') }}</div>
                </div>
                @if($reconciliation->completed_at)
                    <div>
                        <label class="text-xs uppercase tracking-wide text-[#171717]/60">Diselesaikan</label>
                        <div>{{ $reconciliation->completed_at->translatedFormat('d M Y H:i') }}</div>
                    </div>
                @endif
                @if($reconciliation->notes)
                    <div>
                        <label class="text-xs uppercase tracking-wide text-[#171717]/60">Catatan</label>
                        <div class="mt-1 whitespace-pre-wrap">{{ $reconciliation->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Balance Comparison --}}
        <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 bg-[#F5E64D]">
                <h2 class="font-['Space_Grotesk'] text-lg font-semibold">Perbandingan Saldo</h2>
            </div>
            <div class="p-4 space-y-4">
                <div class="border-2 border-[#171717] p-4">
                    <div class="text-xs uppercase tracking-wide text-[#171717]/60">Saldo Sistem</div>
                    <div class="mt-1 font-['Space_Grotesk'] text-2xl font-semibold text-[#166534]">
                        Rp{{ number_format($reconciliation->system_balance, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-[#171717]/60">Dari transaksi kas terealisasi</div>
                </div>
                <div class="border-2 border-[#171717] p-4">
                    <div class="text-xs uppercase tracking-wide text-[#171717]/60">Saldo Aktual Bank</div>
                    <div class="mt-1 font-['Space_Grotesk'] text-2xl font-semibold text-[#171717]">
                        Rp{{ number_format($reconciliation->actual_bank_balance, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-[#171717]/60">Dari rekening koran / cek fisik</div>
                </div>
                <div class="border-2 border-[#171717] p-4 bg-{{ $reconciliation->difference === 0 ? '#B9F4C8' : ($reconciliation->difference > 0 ? '#F5E64D' : '#B42318/20') }}">
                    <div class="text-xs uppercase tracking-wide text-[#171717]/60">Selisih</div>
                    <div class="mt-1 font-['Space_Grotesk'] text-2xl font-semibold {{ $reconciliation->difference === 0 ? 'text-[#166534]' : ($reconciliation->difference > 0 ? 'text-[#166534]' : 'text-[#B42318]') }}">
                        {{ $reconciliation->difference >= 0 ? '+' : '' }}Rp{{ number_format(abs($reconciliation->difference), 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-[#171717]/60">
                        @if($reconciliation->difference === 0)
                            Saldo sesuai. Tidak ada perbedaan.
                        @elseif($reconciliation->difference > 0)
                            Saldo bank lebih besar dari catatan sistem. Periksa pemasukan yang belum tercatat.
                        @else
                            Saldo bank lebih kecil dari catatan sistem. Periksa pengeluaran yang belum tercatat.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bank Statement Link (if exists) --}}
    @if($reconciliation->bankStatement)
        <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717] mb-6">
            <div class="border-b-2 border-[#171717] px-4 py-3 bg-[#F5E64D]">
                <h2 class="font-['Space_Grotesk'] text-lg font-semibold">Rekening Koran Terkait</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs uppercase tracking-wide text-[#171717]/60">Periode</label>
                        <div>{{ $reconciliation->bankStatement->period_start->format('d/m/Y') }} - {{ $reconciliation->bankStatement->period_end->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-[#171717]/60">Saldo Awal Koran</label>
                        <div>Rp{{ number_format($reconciliation->bankStatement->opening_balance, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-[#171717]/60">Saldo Akhir Koran</label>
                        <div>Rp{{ number_format($reconciliation->bankStatement->closing_balance, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-[#171717]/60">Status Koran</label>
                        <div>{{ ucfirst($reconciliation->bankStatement->status) }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Audit Log --}}
    <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
        <div class="border-b-2 border-[#171717] px-4 py-3 bg-[#F5E64D]">
            <h2 class="font-['Space_Grotesk'] text-lg font-semibold">Riwayat Audit</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead>
                    <tr class="border-b-2 border-[#171717]">
                        <th class="text-left px-4 py-2">Waktu</th>
                        <th class="text-left px-4 py-2">Aksi</th>
                        <th class="text-left px-4 py-2">Pengguna</th>
                        <th class="text-left px-4 py-2">Detail</th>
                        <th class="text-left px-4 py-2">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $audits = \App\Models\AuditLog::where('auditable_type', \App\Models\Reconciliation::class)
                            ->where('auditable_id', $reconciliation->id)
                            ->with('user')
                            ->orderBy('created_at')
                            ->get();
                    @endphp
                    @forelse($audits as $audit)
                        <tr class="border-b border-[#DDDDDD]">
                            <td class="px-4 py-2 whitespace-nowrap">{{ $audit->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td class="px-4 py-2">{{ ucfirst($audit->action) }}</td>
                            <td class="px-4 py-2">{{ $audit->user->name ?? 'System' }}</td>
                            <td class="px-4 py-2 font-mono text-xs">
                                @php
                                    $vals = $audit->after_values ?? $audit->before_values;
                                    if ($vals) {
                                        echo 'Sys: Rp' . number_format($vals['system_balance'] ?? 0, 0, ',', '.') . ' | Act: Rp' . number_format($vals['actual_bank_balance'] ?? 0, 0, ',', '.') . ' | Diff: ' . ($vals['difference'] >= 0 ? '+' : '') . number_format($vals['difference'] ?? 0, 0, ',', '.');
                                    }
                                @endphp
                            </td>
                            <td class="px-4 py-2">{{ $audit->reason ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-[#171717]/70">Belum ada riwayat audit</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Actions --}}
    @if($reconciliation->status !== 'completed')
        <div class="mt-4 flex gap-2">
            <a href="{{ route('reconciliations.edit', $reconciliation) }}" class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Edit</a>
            <form method="POST" action="{{ route('reconciliations.destroy', $reconciliation) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus rekonsiliasi ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717] text-[#B42318]">Hapus</button>
            </form>
        </div>
    @endif
</x-app-layout>