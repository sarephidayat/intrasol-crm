<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'INTRASOL Cash Management') }}</title>

    <link rel="icon" type="image/webp" href="{{ asset('images/logo.webp') }}">
    <link rel="shortcut icon" type="image/webp" href="{{ asset('images/logo.webp') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F7F5EF] text-[#171717]">
    <div class="min-h-screen">
        @include('layouts.partials.sidebar')
        <div class="lg:pl-72">
            @include('layouts.partials.topbar')
            <main class="px-4 sm:px-6 lg:px-8 py-6">
                @if (session('status'))
                    <div class="mb-4 border-2 border-[#171717] bg-[#B9F4C8] px-4 py-3 shadow-[4px_4px_0_#171717]">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 border-2 border-[#171717] bg-[#F5E64D] px-4 py-3 shadow-[4px_4px_0_#171717]">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
    <script>
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        function openSidebar(){ sidebar?.classList.remove('-translate-x-full'); overlay?.classList.remove('hidden'); }
        function closeSidebar(){ sidebar?.classList.add('-translate-x-full'); overlay?.classList.add('hidden'); }
        sidebarToggle?.addEventListener('click', openSidebar);
        sidebarClose?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
    </script>
</body>
</html>
