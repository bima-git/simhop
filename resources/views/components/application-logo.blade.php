@props([
    'size' => 'default', // 'default' (10/40px), 'sm' (8/32px), 'lg' (12/48px)
])

@php
    $svgSizes = [
        'sm' => 'w-8 h-8',
        'default' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
    ];
    $svgClass = $svgSizes[$size] ?? 'w-10 h-10';

    $titleSizes = [
        'sm' => 'text-base',
        'default' => 'text-lg',
        'lg' => 'text-2xl',
    ];
    $titleClass = $titleSizes[$size] ?? 'text-lg';

    $subSizes = [
        'sm' => 'text-[10px]',
        'default' => 'text-[11px]',
        'lg' => 'text-xs',
    ];
    $subClass = $subSizes[$size] ?? 'text-[11px]';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <!-- "The S-Hop Monogram" Icon from favicon.svg -->
    <div class="flex items-center justify-center shrink-0">
        <img src="{{ asset('favicon.svg') }}?v={{ file_exists(public_path('favicon.svg')) ? filemtime(public_path('favicon.svg')) : '2' }}" class="{{ $svgClass }} shrink-0 object-contain" alt="SIMHOP Logo">
    </div>

    <!-- Official Typography -->
    <div class="flex flex-col text-left leading-none">
        <span class="font-bold tracking-tight text-slate-900 dark:text-white {{ $titleClass }}">SIMHOP</span>
        <span class="{{ $subClass }} font-medium text-slate-600 dark:text-slate-400 tracking-wide mt-0.5">Monitoring & Handover</span>
    </div>
</div>
