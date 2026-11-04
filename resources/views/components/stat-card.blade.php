@props([
    'title',
    'value',
    'description' => null,
    'color' => 'blue',
    'href' => null,
])

@php
    $colorClasses = match ($color) {
        'emerald' => [
            'iconBg' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400',
            'border' => 'hover:border-emerald-300 dark:hover:border-emerald-700',
        ],
        'amber' => [
            'iconBg' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400',
            'border' => 'hover:border-amber-300 dark:hover:border-amber-700',
        ],
        'rose', 'red' => [
            'iconBg' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400',
            'border' => 'hover:border-rose-300 dark:hover:border-rose-700',
        ],
        'purple' => [
            'iconBg' => 'bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400',
            'border' => 'hover:border-purple-300 dark:hover:border-purple-700',
        ],
        default => [
            'iconBg' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400',
            'border' => 'hover:border-blue-300 dark:hover:border-blue-700',
        ],
    };

    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 p-5 shadow-sm transition-all flex items-start justify-between gap-4 ' . ($href ? 'hover:shadow-md cursor-pointer ' . $colorClasses['border'] : '')]) }}
>
    <div class="flex-1">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
            {{ $title }}
        </span>
        <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
            {{ $value }}
        </div>
        @if($description)
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 flex items-center gap-1">
                {{ $description }}
            </p>
        @endif
        @if(isset($subslot))
            <div class="mt-2">
                {{ $subslot }}
            </div>
        @endif
    </div>

    @if(isset($icon))
        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $colorClasses['iconBg'] }}">
            {{ $icon }}
        </div>
    @endif
</{{ $tag }}>
