<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Dashboard Mahasiswa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Ringkasan Status Video -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-amber-50 p-6 shadow-sm border border-amber-200 dark:bg-amber-950/30 dark:border-amber-800">
                    <p class="text-sm font-medium text-amber-700 dark:text-amber-400">Video Menunggu Review</p>
                    <p class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-200">{{ $videoCounts['pending'] }}</p>
                </div>
                <div class="rounded-lg bg-orange-50 p-6 shadow-sm border border-orange-200 dark:bg-orange-950/30 dark:border-orange-800">
                    <p class="text-sm font-medium text-orange-700 dark:text-orange-400">Video Perlu Revisi</p>
                    <p class="mt-2 text-3xl font-bold text-orange-900 dark:text-orange-200">{{ $videoCounts['revision'] }}</p>
                </div>
                <div class="rounded-lg bg-green-50 p-6 shadow-sm border border-green-200 dark:bg-green-950/30 dark:border-green-800">
                    <p class="text-sm font-medium text-green-700 dark:text-green-400">Video Disetujui</p>
                    <p class="mt-2 text-3xl font-bold text-green-900 dark:text-green-200">{{ $videoCounts['approved'] }}</p>
                </div>
            </div>

            <!-- Proyek yang Diikuti -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <h3 class="mb-4 text-lg font-semibold">Proyek yang Diikuti</h3>
                @forelse ($projects as $project)
                    <div class="mb-4 rounded-lg border border-gray-200 p-4 last:mb-0 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h4 class="font-medium text-base text-gray-900 dark:text-gray-100">
                                <a href="{{ route('mahasiswa.projects.show', $project) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ $project->name }}</a>
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Coach: {{ $project->creator ? $project->creator->name : '-' }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ $project->description ?: 'Tidak ada deskripsi proyek.' }}
                        </p>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Status: {{ ucfirst($project->status) }}
                            @if ($project->start_date) · {{ $project->start_date->format('d M Y') }} @endif
                            @if ($project->end_date) – {{ $project->end_date->format('d M Y') }} @endif
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-gray-100 dark:border-gray-700/60 pt-3 text-xs">
                            <span class="text-gray-500 dark:text-gray-400">
                                📄 {{ $project->handover_documents_count }} Dokumen Handover
                            </span>
                            <a href="{{ route('mahasiswa.projects.show', $project) }}" class="text-indigo-600 hover:underline dark:text-indigo-400 font-medium">
                                Buka Dokumen Handover & Detail →
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Anda belum terdaftar pada proyek magang apa pun.</p>
                @endforelse
            </div>

            <!-- Tugas Aktif -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Tugas Aktif</h3>
                    <a href="{{ route('mahasiswa.tasks.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                        Lihat Semua Tugas →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                <th class="py-3 px-4">Judul Tugas</th>
                                <th class="py-3 px-4">Proyek</th>
                                <th class="py-3 px-4">Tenggat</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Progres</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($activeTasks as $task)
                                <tr>
                                    <td class="py-3 px-4 font-medium">
                                        {{ $task->title }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        {{ $task->project ? $task->project->name : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$task->status" />
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $task->progress }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $task->progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('mahasiswa.tasks.show', $task) }}" class="text-indigo-600 hover:underline dark:text-indigo-400 font-medium">
                                            Detail & Update
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 px-4 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada tugas aktif yang perlu dikerjakan saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
