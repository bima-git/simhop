<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIMHOP') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v={{ file_exists(public_path('favicon.svg')) ? filemtime(public_path('favicon.svg')) : '2' }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v={{ file_exists(public_path('favicon.png')) ? filemtime(public_path('favicon.png')) : '2' }}">
        <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : '2' }}">

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
                <a href="/" class="inline-flex items-center justify-center group focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl p-1 transition">
                    <x-application-logo size="lg" class="justify-center" />
                </a>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-3 font-medium">
                    Portal Monitoring & Handover Proyek Magang &bull; Studi Kasus PT Teknologi Server Indonesia
                </p>
            </div>

            <!-- Login Card -->
            <div class="w-full bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700/80 rounded-xl p-6 sm:p-8">
                {{ $slot }}
            </div>

            <!-- Academic Footer -->
            <div class="mt-6 text-center text-xs text-slate-600 dark:text-slate-400 font-medium">
                Tugas Akhir D3 Teknik Informatika &bull; Politeknik Negeri Cilacap
            </div>
        </div>
    </body>
</html>
