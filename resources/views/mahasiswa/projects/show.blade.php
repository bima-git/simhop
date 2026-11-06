<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            @php
                $backUrl = route('mahasiswa.dashboard');
                $backLabel = 'Kembali ke dashboard';
                if (request()->filled('from_task') && is_numeric(request('from_task'))) {
                    $backUrl = route('mahasiswa.tasks.show', request('from_task'));
                    $backLabel = 'Kembali ke detail tugas';
                } elseif (url()->previous() && url()->previous() !== url()->current() && str_contains(url()->previous(), '/mahasiswa/tasks/')) {
                    $backUrl = url()->previous();
                    $backLabel = 'Kembali ke detail tugas';
                }
            @endphp
            <a href="{{ $backUrl }}"
               aria-label="{{ $backLabel }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                        {{ $project->name }}
                    </h2>
                    <x-status-badge :status="$project->status" />
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                    Coach Pembimbing: <strong class="text-slate-700 dark:text-slate-300">{{ $project->creator ? $project->creator->name : '-' }}</strong>
                    @if ($project->start_date) &bull; Periode: {{ $project->start_date->format('d M Y') }} @endif
                    @if ($project->end_date) s/d {{ $project->end_date->format('d M Y') }} @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            {{-- Info Ringkasan Proyek --}}
            <x-card>
                <x-slot name="title">Deskripsi Proyek</x-slot>
                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                    {{ $project->description ?: 'Tidak ada deskripsi detail untuk proyek ini.' }}
                </p>
            </x-card>

            {{-- Dokumen Handover --}}
            @php
                $typeLabels = [
                    'guide' => 'Panduan (Guide)',
                    'tech_stack' => 'Tech Stack',
                    'migration' => 'Catatan Migrasi',
                    'asset' => 'Aset Pendukung',
                ];
            @endphp

            @forelse ($project->handoverDocuments->groupBy('type') as $type => $docs)
                <x-card>
                    <x-slot name="title">
                        <div class="flex items-center gap-2">
                            <span>{{ $typeLabels[$type] ?? ucfirst($type) }}</span>
                            <x-status-badge :status="$type" />
                        </div>
                    </x-slot>
                    <x-slot name="subtitle">
                        {{ count($docs) }} berkas / panduan teknis tersedia
                    </x-slot>

                    <div class="space-y-4">
                        @foreach ($docs as $doc)
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700/80 p-5 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-600 transition">
                                <h5 class="font-bold text-base text-slate-900 dark:text-slate-100">
                                    {{ $doc->title }}
                                </h5>

                                @if ($doc->content)
                                    <div class="mt-3 p-4 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 font-mono text-xs text-slate-800 dark:text-slate-200 whitespace-pre-wrap leading-relaxed overflow-x-auto shadow-inner">{{ $doc->content }}</div>
                                @endif

                                @if ($doc->file_name)
                                    <div class="mt-3.5 flex flex-wrap items-center justify-between gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                                        <div class="flex items-center gap-2.5 text-xs">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.373L8.557 18.315a1.5 1.5 0 11-2.121-2.121l9.88-9.879" />
                                            </svg>
                                            <span class="font-mono text-slate-700 dark:text-slate-200 font-medium">{{ $doc->file_name }}</span>
                                            <span class="font-mono text-slate-600 dark:text-slate-400">({{ $doc->file_size }} KB)</span>
                                        </div>
                                        <a href="{{ route('handover.download', $doc) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                            Unduh
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @empty
                <x-card>
                    <div class="py-10 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center mx-auto mb-3" aria-hidden="true">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-sm text-slate-800 dark:text-slate-200">Belum ada dokumen handover pada proyek ini.</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                            Coach belum mengunggah dokumen teknis serah terima pada proyek ini.
                        </p>
                    </div>
                </x-card>
            @endforelse

            <div class="pt-2">
                <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>