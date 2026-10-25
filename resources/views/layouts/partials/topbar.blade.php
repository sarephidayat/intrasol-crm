<header class="sticky top-0 z-30 bg-[#F7F5EF]/95 backdrop-blur border-b-2 border-[#171717]">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button id="sidebar-toggle" class="lg:hidden border-2 border-[#171717] px-2 py-1 shadow-[2px_2px_0_#171717]">Menu</button>
            <div>
                <div class="font-['Space_Grotesk'] text-lg sm:text-xl lg:text-2xl font-semibold leading-tight">@yield('page-title', 'Dashboard')</div>
                <div class="text-xs sm:text-sm leading-tight">@yield('page-subtitle', 'INTRASOL Cash Management')</div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:block text-sm">{{ Auth::user()->name ?? Auth::user()->email }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="border-2 border-[#171717] px-3 py-1 text-sm bg-white shadow-[2px_2px_0_#171717] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0_#171717]">Keluar</button>
            </form>
        </div>
    </div>
</header>
