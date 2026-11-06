<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Dashboard Mahasiswa
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                    Pantau tugas aktif, dokumentasi handover proyek, dan status review video Anda.
                </p>
            </div>
            <div>
                <a href="{{ route('mahasiswa.tasks.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Lihat Semua Tugas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            {{-- Ringkasan Status Video --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-stat-card
                    title="Video Menunggu Review"
                    :value="$videoCounts['pending']"
                    description="Sedang ditinjau oleh Coach"
                    color="amber"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icon>
                </x-stat-card>

                <x-stat-card
                    title="Video Perlu Revisi"
                    :value="$videoCounts['revision']"
                    :description="$videoCounts['revision'] > 0 ? 'Segera periksa catatan Coach' : 'Tidak ada revisi pending'"
                    color="amber"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </x-slot:icon>
                </x-stat-card>

                <x-stat-card
                    title="Video Disetujui (Approved)"
                    :value="$videoCounts['approved']"
                    description="Tugas telah selesai tuntas"
                    color="emerald"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icon>
                </x-stat-card>
            </div>

            <!-- Tugas Aktif -->
            <x-card title="Tugas Aktif" subtitle="Daftar tugas yang harus dikerjakan terurut berdasarkan tenggat waktu">
                <x-slot:actions>
                    <a href="{{ route('mahasiswa.tasks.index') }}" aria-label="Lihat semua daftar tugas saya" class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:underline">
                        Lihat Semua Tugas &rarr;
                    </a>
                </x-slot:actions>

                <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700/60 text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Judul Tugas</th>
                                <th class="py-3 px-4">Proyek</th>
                                <th class="py-3 px-4">Tenggat</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Progres</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @forelse ($activeTasks as $task)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $task->title }}
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300">
                                        {{ $task->project ? $task->project->name : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-xs whitespace-nowrap">
                                        @if ($task->due_date)
                                            @if ($task->due_date->isPast() && $task->status !== 'done')
                                                <span class="inline-flex items-center gap-1 font-semibold text-rose-600 dark:text-rose-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $task->due_date->format('d M Y') }} (Terlewat)
                                                </span>
                                            @elseif ($task->due_date->isToday())
                                                <span class="inline-flex items-center gap-1 font-semibold text-amber-600 dark:text-amber-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Hari Ini ({{ $task->due_date->format('d M') }})
                                                </span>
                                            @else
                                                <span class="text-slate-600 dark:text-slate-300">{{ $task->due_date->format('d M Y') }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-600 dark:text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <x-status-badge :status="$task->status" />
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-20 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $task->progress }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $task->progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <a href="{{ route('mahasiswa.tasks.show', $task) }}" aria-label="Detail dan update tugas {{ $task->title }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 rounded-lg border border-blue-200 dark:border-blue-800 transition">
                                            Detail & Update
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-xs text-slate-600 dark:text-slate-400">
                                        Tidak ada tugas aktif yang perlu dikerjakan saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Proyek yang Diikuti & Akses Dokumen Handover -->
            <x-card title="Proyek yang Diikuti" subtitle="Proyek magang tempat Anda ditugaskan beserta dokumentasi teknisnya">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse ($projects as $project)
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700/80 p-5 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between hover:border-blue-300 dark:hover:border-blue-700 transition">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="font-bold text-base text-slate-900 dark:text-white leading-tight">
                                        <a href="{{ route('mahasiswa.projects.show', $project) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $project->name }}
                                        </a>
                                    </h4>
                                    <x-status-badge :status="$project->status" />
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                    Coach: <strong class="text-slate-700 dark:text-slate-300">{{ $project->creator ? $project->creator->name : '-' }}</strong>
                                </p>
                                <p class="mt-2 text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">
                                    {{ $project->description ?: 'Tidak ada deskripsi proyek.' }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between text-xs">
                                <span class="inline-flex items-center gap-1.5 font-medium text-slate-600 dark:text-slate-400">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ $project->handover_documents_count }} Dokumen Handover
                                </span>
                                <a href="{{ route('mahasiswa.projects.show', $project) }}" aria-label="Buka dokumen handover dan detail proyek {{ $project->name }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold flex items-center gap-1">
                                    Buka Dokumen Handover & Detail &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 py-8 text-center text-xs text-slate-600 dark:text-slate-400">
                            Anda belum terdaftar pada proyek magang apa pun.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
