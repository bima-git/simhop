@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-sm overflow-hidden']) }}>
    @if($title || isset($header) || isset($actions))
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-4">
            <div>
                @if(isset($header))
                    {{ $header }}
                @else
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white leading-tight">
                        {{ $title }}
                    </h3>
                    @if($subtitle)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $subtitle }}
                        </p>
                    @endif
                @endif
            </div>
            @if(isset($actions))
                <div class="flex items-center gap-2 shrink-0">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-5 sm:p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700/60 text-xs text-slate-500 dark:text-slate-400">
            {{ $footer }}
        </div>
    @endif
</div>
