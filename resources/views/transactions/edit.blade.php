<x-app-layout>
    @section('page-title', 'Edit Transaksi Kas')
    @section('page-subtitle', $transaction->description)

    <div class="max-w-2xl mx-auto">
        <div class="card">
            <div class="card-header">Edit Transaksi</div>
            <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data" class="card-body space-y-4">
                @csrf
                @method('PUT')

                {{-- Row 1: Date & Type --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="transaction_date" class="form-label">Tanggal Transaksi <span class="text-[#B42318]">*</span></label>
                        <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required class="form-input">
                    </div>
                    <div>
                        <label for="type" class="form-label">Jenis <span class="text-[#B42318]">*</span></label>
                        <select name="type" id="type" required class="form-select" onchange="filterCategories()">
                            <option value="expense" @selected(old('type', $transaction->type)==='expense')>Pengeluaran</option>
                            <option value="income" @selected(old('type', $transaction->type)==='income')>Pemasukan</option>
                        </select>
                    </div>
                </div>

                {{-- Row 2: Amount & Category --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="form-label">Jumlah (IDR) <span class="text-[#B42318]">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#171717]/60">Rp</span>
                            <input type="number" name="amount" id="amount" min="1" step="1" value="{{ old('amount', $transaction->amount) }}" required class="form-input pl-8">
                        </div>
                    </div>
                    <div>
                        <label for="category_id" class="form-label">Kategori <span class="text-[#B42318]">*</span></label>
                        <select name="category_id" id="category_id" required class="form-select">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" data-type="{{ $c->type }}" @selected(old('category_id', $transaction->category_id)==$c->id)>{{ $c->name }} ({{ $c->type }})</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-[10px] text-[#B42318]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="form-label">Keterangan <span class="text-[#B42318]">*</span></label>
                    <input name="description" id="description" value="{{ old('description', $transaction->description) }}" required class="form-input">
                </div>

                {{-- Row 3: Party & Project --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="party_id" class="form-label">Pihak</label>
                        <select name="party_id" id="party_id" class="form-select">
                            <option value="">-</option>
                            @foreach($parties as $p)
                                <option value="{{ $p->id }}" @selected(old('party_id', $transaction->party_id)==$p->id)>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="project_id" class="form-label">Proyek</label>
                        <select name="project_id" id="project_id" class="form-select">
                            <option value="">-</option>
                            @foreach($projects as $pr)
                                <option value="{{ $pr->id }}" @selected(old('project_id', $transaction->project_id)==$pr->id)>{{ $pr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Project Related Checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="project_related" value="1" @checked(old('project_related', $transaction->project_id)) class="w-4 h-4 border-2 border-[#171717] rounded">
                    <span class="text-sm">Terkait proyek (wajib isi proyek)</span>
                </label>
                @error('project_id')
                    <p class="text-[10px] text-[#B42318]">{{ $message }}</p>
                @enderror

                {{-- Receipt Upload --}}
                <div>
                    <label for="receipt" class="form-label">Bukti/Struk (jpg, jpeg, png, pdf, maks 2MB)</label>
                    <input type="file" name="receipt" id="receipt" class="form-input bg-white">
                    @if($transaction->receipt_path)
                        <p class="mt-1 text-sm text-[#171717]/70">
                            File saat ini: <a href="{{ route('receipts.download', $transaction) }}" class="underline">{{ basename($transaction->receipt_path) }}</a>
                            <span class="mx-2">|</span>
                            <a href="{{ route('receipts.view', $transaction) }}" class="underline" target="_blank">Lihat</a>
                        </p>
                    @endif
                    @error('receipt')
                        <p class="mt-1 text-[10px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Reason (required for edit) --}}
                <div>
                    <label for="reason" class="form-label">Alasan Perubahan <span class="text-[#B42318]">*</span></label>
                    <textarea name="reason" id="reason" rows="2" required class="form-input">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-[10px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-[10px] text-[#171717]/60">Wajib diisi untuk audit trail</p>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t-2 border-[#171717]">
                    <button type="submit" class="btn-primary flex-1">Simpan Perubahan</button>
                    <a href="{{ route('transactions.show', $transaction) }}" class="btn-secondary flex-1 text-center">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Filter categories based on selected type
        function filterCategories() {
            const type = document.getElementById('type').value;
            const categorySelect = document.getElementById('category_id');
            const options = categorySelect.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }
                const optionType = option.dataset.type;
                if (type === '' || optionType === type) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });

            // Reset selection if current category doesn't match type
            if (categorySelect.value) {
                const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                if (selectedOption.dataset.type !== type && selectedOption.value !== '') {
                    categorySelect.value = '';
                }
            }
        }

        // Run on load
        document.addEventListener('DOMContentLoaded', filterCategories);
    </script>
</x-app-layout>