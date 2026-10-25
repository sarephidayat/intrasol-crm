<x-app-layout>
    @section('page-title', 'Tambah Pihak')
    @section('page-subtitle', 'Klien/Vendor')

    <div class="max-w-2xl border-2 border-[#171717] bg-white shadow-[4px_4px_0_#171717]">
        <form method="POST" action="{{ route('parties.store') }}" class="p-4 space-y-3">
            @csrf
            <div>
                <label class="block text-sm mb-1">Nama Pihak *</label>
                <input name="name" value="{{ old('name') }}" required class="w-full border-2 border-[#171717] px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Peran *</label>
                <select name="role" required class="w-full border-2 border-[#171717] px-3 py-2">
                    <option value="both" @selected(old('role')==='both')>Keduanya</option>
                    <option value="client" @selected(old('role')==='client')>Klien</option>
                    <option value="vendor" @selected(old('role')==='vendor')>Vendor</option>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div><label class="block text-sm mb-1">Nama PIC</label><input name="primary_pic_name" value="{{ old('primary_pic_name') }}" class="w-full border-2 border-[#171717] px-3 py-2"></div>
                <div><label class="block text-sm mb-1">Email PIC</label><input name="primary_pic_email" value="{{ old('primary_pic_email') }}" class="w-full border-2 border-[#171717] px-3 py-2"></div>
                <div><label class="block text-sm mb-1">Telepon PIC</label><input name="primary_pic_phone" value="{{ old('primary_pic_phone') }}" class="w-full border-2 border-[#171717] px-3 py-2"></div>
            </div>
            <div><label class="block text-sm mb-1">Alamat</label><textarea name="address" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('address') }}</textarea></div>
            <div><label class="block text-sm mb-1">Catatan</label><textarea name="notes" class="w-full border-2 border-[#171717] px-3 py-2">{{ old('notes') }}</textarea></div>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
            <div class="flex gap-2">
                <button class="border-2 border-[#171717] px-3 py-1 bg-[#F5E64D] shadow-[2px_2px_0_#171717]">Simpan</button>
                <a href="{{ route('parties.index') }}" class="border-2 border-[#171717] px-3 py-1 bg-white shadow-[2px_2px_0_#171717]">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
