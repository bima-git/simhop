<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIMHOP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 dark:text-slate-100 antialiased bg-slate-50 dark:bg-slate-900 min-h-screen flex flex-col justify-center items-center py-10 px-4">
        <div class="w-full sm:max-w-md flex flex-col items-center">
            <!-- Brand Header -->
            <div class="text-center mb-6">
                <a href="/" class="inline-flex items-center gap-3 group">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-blue-600 text-white shadow-sm group-hover:bg-blue-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">SIMHOP</span>
                </a>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">
                    Portal Monitoring & Handover Proyek Magang &bull; Studi Kasus PT Xcode Indonesia
                </p>
            </div>

            <!-- Login Card -->
            <div class="w-full bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700/80 rounded-xl p-6 sm:p-8">
                {{ $slot }}
            </div>

            <!-- Academic Footer -->
            <div class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
                Tugas Akhir D3 Teknik Informatika &bull; Politeknik Negeri Cilacap
            </div>
        </div>
    </body>
</html>
