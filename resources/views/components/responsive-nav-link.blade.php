@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-blue-600 dark:border-blue-400 text-start text-base font-semibold text-blue-700 dark:text-blue-400 bg-blue-50/70 dark:bg-blue-950/40 focus:outline-none focus:text-blue-800 dark:focus:text-blue-300 focus:bg-blue-100 dark:focus:bg-blue-950/60 focus:border-blue-700 dark:focus:border-blue-300 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:text-slate-900 focus:bg-slate-50 focus:border-slate-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
