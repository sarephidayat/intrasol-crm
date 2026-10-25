<x-app-layout>
    @section('page-title', 'Edit Rekonsiliasi')
    @section('page-subtitle', 'Bank Jago')

    <div class="mb-4">
        <a href="{{ route('reconciliations.show', $reconciliation) }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0_#171717] inline-block">← Kembali</a>
    </div>

    <div class="max-w-2xl">
        <div class="border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
            <div class="border-b-2 border-[#171717] px-4 py-3 bg-[#F5E64D]">
                <h2 class="font-['Space_Grotesk'] text-lg font-semibold">Edit Rekonsiliasi</h2>
            </div>

            <form method="POST" action="{{ route('reconciliations.update', $reconciliation) }}" class="p-4 space-y-4">
                @csrf
                @method('PUT')

                {{-- System Balance Info --}}
                <div class="border-2 border-[#171717] bg-[#F7F5EF] p-4">
                    <div class="text-sm text-[#171717]/70">Saldo Sistem (Otomatis, dihitung ulang)</div>
                    <div class="mt-1 font-['Space_Grotesk'] text-2xl font-semibold text-[#166534]">
                        Rp{{ number_format($systemBalance ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-[#171717]/60">Dihitung dari transaksi kas terealisasi saat ini</div>
                </div>

                {{-- Reconciliation Date --}}
                <div>
                    <label for="reconciliation_date" class="block text-sm mb-1">Tanggal Rekonsiliasi <span class="text-red-600">*</span></label>
                    <input type="date" name="reconciliation_date" id="reconciliation_date" value="{{ old('reconciliation_date', $reconciliation->reconciliation_date->format('Y-m-d')) }}" required
                        class="w-full border-2 border-[#171717] px-3 py-2 @error('reconciliation_date') border-red-600 @enderror">
                    @error('reconciliation_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actual Bank Balance --}}
                <div>
                    <label for="actual_bank_balance" class="block text-sm mb-1">Saldo Aktual Bank Jago <span class="text-red-600">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#171717]/60">Rp</span>
                        <input type="number" name="actual_bank_balance" id="actual_bank_balance" value="{{ old('actual_bank_balance', $reconciliation->actual_bank_balance) }}" required min="0" step="1"
                            class="w-full border-2 border-[#171717] px-3 py-2 pl-8 @error('actual_bank_balance') border-red-600 @enderror">
                    </div>
                    @error('actual_bank_balance')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Difference Preview --}}
                <div class="border-2 border-[#171717] bg-white p-4" id="differencePreview">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-[#171717]/60">Selisih</div>
                            <div class="font-['Space_Grotesk'] text-xl font-semibold" id="differenceText">Rp0</div>
                        </div>
                        <div class="text-right" id="differenceStatus"></div>
                    </div>
                    <div class="mt-2 text-xs text-[#171717]/60">
                        Selisih = Saldo Aktual - Saldo Sistem. Positif: saldo bank lebih besar dari catatan. Negatif: saldo bank lebih kecil.
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm mb-1">Catatan</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('notes', $reconciliation->notes) }}</textarea>
                    <p class="mt-1 text-xs text-[#171717]/60">Alasan selisih, catatan pemeriksaan, dll (opsional)</p>
                </div>

                {{-- Reason (required for edit) --}}
                <div>
                    <label for="reason" class="block text-sm mb-1">Alasan Perubahan <span class="text-red-600">*</span></label>
                    <textarea name="reason" id="reason" rows="2" required class="w-full border-2 border-[#171717] px-3 py-2 @error('reason') border-red-600 @enderror">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-[#171717]/60">Wajib diisi untuk audit trail</p>
                </div>

                {{-- Submit --}}
                <div class="flex gap-2 pt-2 border-t-2 border-[#171717]">
                    <button type="submit" class="border-2 border-[#171717] px-4 py-2 bg-[#F5E64D] shadow-[2px_2px_0_#171717] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0_#171717]">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('reconciliations.show', $reconciliation) }}" class="border-2 border-[#171717] px-4 py-2 bg-white shadow-[2px_2px_0_#171717] flex items-center justify-center">
                        Batal
                    </a>
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
                diffText.className = 'font-[\'Space_Grotesk\'] text-xl font-semibold ' + (diff === 0 ? 'text-[#166534]' : (diff > 0 ? 'text-[#166534]' : 'text-[#B42318]'));

                if (diff === 0) {
                    diffStatus.innerHTML = '<span class="inline-block border-2 border-[#171717] bg-[#B9F4C8] px-2 py-0.5 text-xs text-[#166534]">Sesuai</span>';
                } else if (diff > 0) {
                    diffStatus.innerHTML = '<span class="inline-block border-2 border-[#171717] bg-[#F5E64D] px-2 py-0.5 text-xs">Bank Lebih</span>';
                } else {
                    diffStatus.innerHTML = '<span class="inline-block border-2 border-[#171717] bg-[#B42318] px-2 py-0.5 text-xs text-white">Bank Kurang</span>';
                }
            }

            actualInput.addEventListener('input', updateDifference);
            actualInput.addEventListener('change', updateDifference);

            // Initial calculation
            updateDifference();
        });
    </script>
</x-app-layout>