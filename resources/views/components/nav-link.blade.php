@props(['href', 'active' => false])

@php
$classes = $active ?? false
            ? 'block px-3 py-2 border-2 border-[#171717] bg-[#F5E64D] shadow-[2px_2px_0_#171717] font-medium'
            : 'block px-3 py-2 border-2 border-[#171717] bg-white hover:bg-[#F7F5EF] shadow-[2px_2px_0_#171717] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0_#171717]';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
