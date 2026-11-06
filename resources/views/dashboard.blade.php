<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 dark:text-slate-100 leading-tight">
            {{ __('Dasbor') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 text-slate-900 dark:text-slate-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
