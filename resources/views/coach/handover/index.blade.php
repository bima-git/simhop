<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('coach.projects.show', $project) }}"
                   aria-label="Kembali ke detail proyek"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                        Handover: {{ $project->name }}
                    </h2>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                        Dokumentasi teknis, spesifikasi sistem, dan berkas serah terima proyek
                    </p>
                </div>
            </div>
            <a href="{{ route('coach.handover.create', $project) }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                + Tambah Dokumen
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @php
                $typeLabels = [
                    'guide' => 'Panduan (Guide)',
                    'tech_stack' => 'Tech Stack',
                    'migration' => 'Catatan Migrasi',
                    'asset' => 'Aset Pendukung',
                ];
            @endphp

            @forelse ($documents->groupBy('type') as $type => $docs)
                <x-card>
                    <x-slot name="title">
                        <div class="flex items-center gap-2">
                            <span>{{ $typeLabels[$type] ?? ucfirst($type) }}</span>
                            <x-status-badge :status="$type" />
                        </div>
                    </x-slot>
                    <x-slot name="subtitle">
                        {{ count($docs) }} dokumen tercatat pada kategori ini
                    </x-slot>

                    <div class="space-y-4">
                        @foreach ($docs as $doc)
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700/80 p-5 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-600 transition">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-base text-slate-900 dark:text-slate-100">
                                                {{ $doc->title }}
                                            </h4>
                                        </div>

                                        @if ($doc->content)
                                            <div class="mt-3 p-4 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 font-mono text-xs text-slate-800 dark:text-slate-200 whitespace-pre-wrap leading-relaxed overflow-x-auto shadow-inner">{{ $doc->content }}</div>
                                        @endif

                                        @if ($doc->file_name)
                                            <div class="mt-3.5 inline-flex items-center gap-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2 text-xs">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.373L8.557 18.315a1.5 1.5 0 11-2.121-2.121l9.88-9.879" />
                                                </svg>
                                                <span class="font-mono text-slate-700 dark:text-slate-200 font-medium">{{ $doc->file_name }}</span>
                                                <span class="font-mono text-slate-600 dark:text-slate-400">({{ $doc->file_size }} KB)</span>
                                                <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                                                <a href="{{ route('handover.download', $doc) }}"
                                                   class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 hover:underline">
                                                    Unduh
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200 dark:border-slate-700/60">
                                        <a href="{{ route('coach.handover.edit', $doc) }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                            <svg class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('coach.handover.destroy', $doc) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 px-3 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition">
                                                <svg class="w-3.5 h-3.5 text-rose-500" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @empty
                <x-card>
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center mx-auto mb-3" aria-hidden="true">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-sm text-slate-800 dark:text-slate-200">Belum ada dokumen handover</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                            Mulai tambahkan panduan, spesifikasi tech stack, atau catatan migrasi agar mahasiswa dapat mempelajari proyek.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('coach.handover.create', $project) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-sm transition">
                                + Tambah Dokumen Sekarang
                            </a>
                        </div>
                    </div>
                </x-card>
            @endforelse

            <div class="pt-2">
                <a href="{{ route('coach.projects.show', $project) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                    &larr; Kembali ke Detail Proyek
                </a>
            </div>
        </div>
    </div>
</x-app-layout>