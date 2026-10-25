<x-app-layout>
    @section('page-title', 'Buat Rekonsiliasi')
    @section('page-subtitle', 'Bank Jago')

    <div class="page-header">
        <a href="{{ route('reconciliations.index') }}" class="btn-secondary whitespace-nowrap">← Kembali ke Daftar</a>
    </div>

    <div class="max-w-xl mx-auto">
        <div class="card">
            <div class="card-header">Form Rekonsiliasi</div>

            <form method="POST" action="{{ route('reconciliations.store') }}" class="card-body space-y-4">
                @csrf

                {{-- System Balance Info --}}
                <div class="card p-4">
                    <div class="text-xs text-[#171717]/70">Saldo Sistem (Otomatis)</div>
                    <div class="mt-1 font-['Space_Grotesk'] text-2xl font-semibold text-[#166534]">
                        Rp{{ number_format($systemBalance ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-[10px] text-[#171717]/60">Dihitung dari transaksi kas terealisasi (income - expense)</div>
                </div>

                {{-- Reconciliation Date --}}
                <div>
                    <label for="reconciliation_date" class="form-label">Tanggal Rekonsiliasi <span class="text-[#B42318]">*</span></label>
                    <input type="date" name="reconciliation_date" id="reconciliation_date" value="{{ old('reconciliation_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required class="form-input">
                    @error('reconciliation_date')
                        <p class="mt-1 text-[10px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-[10px] text-[#171717]/60">Tanggal pemeriksaan saldo aktual</p>
                </div>

                {{-- Actual Bank Balance --}}
                <div>
                    <label for="actual_bank_balance" class="form-label">Saldo Aktual Bank Jago <span class="text-[#B42318]">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#171717]/60">Rp</span>
                        <input type="number" name="actual_bank_balance" id="actual_bank_balance" value="{{ old('actual_bank_balance') }}" required min="0" step="1" class="form-input pl-8">
                    </div>
                    @error('actual_bank_balance')
                        <p class="mt-1 text-[10px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-[10px] text-[#171717]/60">Saldo dari rekening koran atau hasil cek kas fisik</p>
                </div>

                {{-- Difference Preview --}}
                <div class="card p-4" id="differencePreview">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-[#171717]/60">Selisih</div>
                            <div class="font-['Space_Grotesk'] text-xl font-semibold" id="differenceText">Rp0</div>
                        </div>
                        <div class="text-right" id="differenceStatus"></div>
                    </div>
                    <div class="mt-2 text-[10px] text-[#171717]/60">
                        Selisih = Saldo Aktual - Saldo Sistem. Positif: saldo bank lebih besar dari catatan. Negatif: saldo bank lebih kecil.
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea name="notes" id="notes" rows="3" class="form-input">{{ old('notes') }}</textarea>
                    <p class="mt-1 text-[10px] text-[#171717]/60">Alasan selisih, catatan pemeriksaan, dll (opsional)</p>
                </div>

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t-2 border-[#171717]">
                    <button type="submit" class="btn-primary flex-1">Simpan Rekonsiliasi</button>
                    <a href="{{ route('reconciliations.index') }}" class="btn-secondary flex-1 text-center">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const systemBalance = {{ $systemBalance ?? 0 }};
            const actualInput = document.getElementById('actual_bank_balance');
            const preview = document.getElementById('differencePreview');
            const diffText = document.getElementById('differenceText');
            const diffStatus = document.getElementById('differenceStatus');

            function updateDifference() {
                const actual = parseInt(actualInput.value) || 0;
                const diff = actual - systemBalance;

                diffText.textContent = (diff >= 0 ? '+' : '') + 'Rp' + diff.toLocaleString('id-ID');
                diffText.className = 'font-['Space_Grotesk'] text-xl font-semibold ' + (diff === 0 ? 'text-[#166534]' : (diff > 0 ? 'text-[#166534]' : 'text-[#B42318]'));

                if (diff === 0) {
                    diffStatus.innerHTML = '<span class="badge-success">Sesuai</span>';
                } else if (diff > 0) {
                    diffStatus.innerHTML = '<span class="badge-warning">Bank Lebih</span>';
                } else {
                    diffStatus.innerHTML = '<span class="badge-danger">Bank Kurang</span>';
                }
            }

            actualInput.addEventListener('input', updateDifference);
            actualInput.addEventListener('change', updateDifference);

            // Initial calculation if value exists
            if (actualInput.value) {
                updateDifference();
            }
        });
    </script>
</x-app-layout>