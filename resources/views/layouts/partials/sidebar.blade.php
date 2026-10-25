<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/40 hidden lg:hidden z-40"></div>

<!-- Sidebar -->
<aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full lg:translate-x-0 transition-transform bg-white border-r-2 border-[#171717] flex flex-col">
    <div class="flex items-center justify-between h-16 px-4 border-b-2 border-[#171717]">
        <div class="flex items-center gap-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center border-2 border-[#171717] bg-white shadow-[2px_2px_0_#171717] overflow-hidden">
                @if (file_exists(public_path('images/logo.webp')))
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo INTRASOL" class="h-8 w-8 object-contain">
                @endif
            </div>
            <div>
                <div class="font-['Space_Grotesk'] text-base font-semibold leading-tight">INTRASOL</div>
                <div class="text-xs leading-tight">Cash Management</div>
            </div>
        </div>
        <button id="sidebar-close" class="lg:hidden border-2 border-[#171717] px-2 py-1 shadow-[2px_2px_0_#171717]">Tutup</button>
    </div>
    <nav class="flex-1 overflow-y-auto px-3 pt-2 pb-3 space-y-2">
        @php
            $utamaActive = request()->routeIs('overview') || request()->routeIs('dashboard');
            $keuanganActive = request()->routeIs('transactions.*') || request()->routeIs('bills.*') || request()->routeIs('advances.*') || request()->routeIs('reconciliations.*');
            $dataMasterActive = request()->routeIs('parties.*') || request()->routeIs('projects.*') || request()->routeIs('categories.*');
            $laporanActive = request()->routeIs('reports.*');
        @endphp

        <div x-data="{ open: {{ $utamaActive ? 'true' : 'false' }} }" class="border-2 border-[#171717] bg-white shadow-[2px_2px_0_#171717]">
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-left">
                <span class="text-[11px] uppercase tracking-wide">Utama</span>
                <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
            <div x-show="open" x-collapse class="px-2 pb-2 space-y-1">
                <x-nav-link :href="route('overview')" :active="request()->routeIs('overview') || request()->routeIs('dashboard')">Dashboard</x-nav-link>
            </div>
        </div>

        <div x-data="{ open: {{ $keuanganActive ? 'true' : 'false' }} }" class="border-2 border-[#171717] bg-white shadow-[2px_2px_0_#171717]">
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-left">
                <span class="text-[11px] uppercase tracking-wide">Keuangan</span>
                <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
            <div x-show="open" x-collapse class="px-2 pb-2 space-y-1">
                <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">Transaksi Kas</x-nav-link>
                <x-nav-link :href="route('bills.index', ['type' => 'receivable'])" :active="request()->routeIs('bills.*') && request('type') !== 'payable'">Piutang</x-nav-link>
                <x-nav-link :href="route('bills.index', ['type' => 'payable'])" :active="request()->routeIs('bills.*') && request('type') === 'payable'">Utang</x-nav-link>
                <x-nav-link :href="route('advances.index')" :active="request()->routeIs('advances.*')">Dana Talangan</x-nav-link>
                <x-nav-link :href="route('reconciliations.index')" :active="request()->routeIs('reconciliations.*')">Rekonsiliasi</x-nav-link>
            </div>
        </div>

        <div x-data="{ open: {{ $dataMasterActive ? 'true' : 'false' }} }" class="border-2 border-[#171717] bg-white shadow-[2px_2px_0_#171717]">
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-left">
                <span class="text-[11px] uppercase tracking-wide">Data Master</span>
                <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
            <div x-show="open" x-collapse class="px-2 pb-2 space-y-1">
                <x-nav-link :href="route('parties.index')" :active="request()->routeIs('parties.*')">Pihak (Klien/Vendor)</x-nav-link>
                <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">Proyek</x-nav-link>
                <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">Kategori</x-nav-link>
            </div>
        </div>

        <div x-data="{ open: {{ $laporanActive ? 'true' : 'false' }} }" class="border-2 border-[#171717] bg-white shadow-[2px_2px_0_#171717]">
            <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-left">
                <span class="text-[11px] uppercase tracking-wide">Laporan</span>
                <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
            <div x-show="open" x-collapse class="px-2 pb-2 space-y-1">
                <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">Laporan</x-nav-link>
            </div>
        </div>
    </nav>
    <div class="p-3 border-t-2 border-[#171717] text-xs">
        &copy; {{ date('Y') }} PT Integrasi Solusi Teknologi
    </div>
</aside>
