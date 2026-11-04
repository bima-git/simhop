<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ $project->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            {{-- Info Proyek --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Status: {{ ucfirst($project->status) }}
                    @if ($project->start_date) · {{ $project->start_date->format('d M Y') }} @endif
                    @if ($project->end_date) – {{ $project->end_date->format('d M Y') }} @endif
                </p>
                <p class="mt-2">{{ $project->description ?: 'Tidak ada deskripsi.' }}</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Coach: {{ $project->creator ? $project->creator->name : '-' }}
                </p>
            </div>

            {{-- Dokumen Handover --}}
            @php
                $typeLabels = [
                    'guide' => 'Panduan (Guide)',
                    'tech_stack' => 'Tech Stack',
                    'migration' => 'Catatan Migrasi',
                    'asset' => 'Aset Pendukung',
                ];
            @endphp

            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <h3 class="mb-4 text-lg font-semibold">Dokumen Handover</h3>

                @forelse ($project->handoverDocuments->groupBy('type') as $type => $docs)
                    <div class="mb-6 last:mb-0">
                        <h4 class="mb-3 font-medium text-gray-700 dark:text-gray-300">{{ $typeLabels[$type] ?? ucfirst($type) }}</h4>
                        <div class="space-y-3">
                            @foreach ($docs as $doc)
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <h5 class="font-medium">{{ $doc->title }}</h5>
                                    @if ($doc->content)
                                        <div class="mt-2 whitespace-pre-line text-sm text-gray-600 dark:text-gray-300">{{ $doc->content }}</div>
                                    @endif
                                    @if ($doc->file_name)
                                        <div class="mt-3 flex items-center gap-2 text-sm">
                                            <span class="text-gray-500 dark:text-gray-400">📎 {{ $doc->file_name }} ({{ $doc->file_size }} KB)</span>
                                            <a href="{{ route('handover.download', $doc) }}"
                                               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1 text-xs font-semibold text-white hover:bg-indigo-500">
                                                Unduh
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada dokumen handover pada proyek ini.</p>
                @endforelse
            </div>

            <a href="{{ route('mahasiswa.dashboard') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">← Kembali ke Dashboard</a>
        </div>
    </div>
</x-app-layout>
