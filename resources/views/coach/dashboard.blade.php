<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard Coach
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('coach.videos.index') }}"
                   class="inline-flex items-center rounded-md bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-500 shadow-sm transition">
                    🎬 Antrean Review
                    @if ($pendingReviewCount > 0)
                        <span class="ms-1.5 rounded-full bg-white px-2 py-0.5 text-xs font-bold text-amber-800">{{ $pendingReviewCount }}</span>
                    @endif
                </a>
                <a href="{{ route('coach.projects.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 shadow-sm transition">
                    + Buat Proyek
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Ringkasan Metrik Coach -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Video Menunggu Review -->
                <div class="rounded-lg bg-amber-50 p-6 shadow-sm border border-amber-200 dark:bg-amber-950/30 dark:border-amber-800">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-400">Antrean Review Video</p>
                        <span class="text-lg">⏳</span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-amber-950 dark:text-amber-100">{{ $pendingReviewCount }}</p>
                    <p class="mt-1 text-xs text-amber-700 dark:text-amber-300">
                        @if ($pendingReviewCount > 0)
                            Memerlukan tindakan review Anda
                        @else
                            Tidak ada antrean pending
                        @endif
                    </p>
                </div>

                <!-- Proyek Saya -->
                <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Proyek Bimbingan</p>
                        <span class="text-lg">📁</span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $projectStats['total'] }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $projectStats['active'] }} aktif · {{ $projectStats['completed'] }} selesai
                    </p>
                </div>

                <!-- Tugas Mahasiswa -->
                <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Tugas</p>
                        <span class="text-lg">📋</span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $taskStats['total'] }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $taskStats['todo'] }} To Do · {{ $taskStats['in_progress'] }} In Progress · {{ $taskStats['done'] }} Done
                    </p>
                </div>

                <!-- Mahasiswa Bimbingan -->
                <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Mahasiswa Bimbingan</p>
                        <span class="text-lg">👥</span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $studentCount }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Peserta magang aktif</p>
                </div>
            </div>

            <!-- Bagian 1: Antrean Video yang Perlu Direview -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <span>Antrean Video Menunggu Review</span>
                        @if ($pendingReviewCount > 0)
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-900 dark:text-amber-200">{{ $pendingReviewCount }} pending</span>
                        @endif
                    </h3>
                    <a href="{{ route('coach.videos.index', ['status' => 'pending']) }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400 font-medium">
                        Lihat Semua Antrean →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                <th class="py-3 px-4">Judul Video</th>
                                <th class="py-3 px-4">Mahasiswa</th>
                                <th class="py-3 px-4">Tugas / Proyek</th>
                                <th class="py-3 px-4">Tanggal Kirim</th>
                                <th class="py-3 px-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($pendingVideos as $video)
                                <tr>
                                    <td class="py-3 px-4 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $video->title }}
                                        <p class="text-xs text-gray-400 font-normal">Platform: {{ str_replace('_', ' ', $video->platform?->value ?? '-') }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $video->user ? $video->user->name : '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $video->user ? ($video->user->nim ?? $video->user->email) : '' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        <p class="font-medium">{{ $video->task ? $video->task->title : '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $video->task && $video->task->project ? $video->task->project->name : '-' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300 text-xs">
                                        {{ $video->created_at->format('d M Y H:i') }} WIB
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('coach.videos.show', $video) }}"
                                           class="inline-flex items-center rounded-md bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-500 transition shadow-sm">
                                            Review Sekarang →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 px-4 text-center text-gray-500 dark:text-gray-400">
                                        🎉 Hebat! Tidak ada video dokumentasi yang sedang menunggu review.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bagian 2: Proyek Bimbingan Terbaru -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Proyek Bimbingan Terbaru</h3>
                    <a href="{{ route('coach.projects.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400 font-medium">
                        Kelola Semua Proyek →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($recentProjects as $project)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700 flex flex-col justify-between hover:border-indigo-300 dark:hover:border-indigo-600 transition">
                            <div>
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold text-base text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('coach.projects.show', $project) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                            {{ $project->name }}
                                        </a>
                                    </h4>
                                    <span class="text-xs uppercase font-semibold px-2 py-0.5 rounded-full
                                        {{ $project->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $project->status }}
                                    </span>
                                </div>
                                <p class="mt-2 text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
                                    {{ $project->description ?: 'Tidak ada deskripsi.' }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs text-gray-500">
                                <span>👥 {{ $project->members_count }} Anggota</span>
                                <span>📋 {{ $project->tasks_count }} Tugas</span>
                                <a href="{{ route('coach.projects.show', $project) }}" class="text-indigo-600 hover:underline dark:text-indigo-400 font-semibold">
                                    Detail →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 py-6 text-center text-gray-500 dark:text-gray-400">
                            Belum ada proyek yang dibuat. Silakan klik tombol "+ Buat Proyek" di atas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
