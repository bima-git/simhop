<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Dashboard Coach
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                    Monitoring kemajuan proyek magang, tugas mahasiswa, dan review video dokumentasi.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('coach.videos.index', ['status' => 'pending']) }}"
                   aria-label="Antrean review video: {{ $pendingReviewCount }} video menunggu review"
                   class="inline-flex items-center gap-2 rounded-lg bg-amber-700 px-3.5 py-2 text-xs font-semibold text-white hover:bg-amber-800 shadow-sm transition">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Antrean Review</span>
                    @if ($pendingReviewCount > 0)
                        <span class="rounded-full bg-white px-2 py-0.5 text-[11px] font-bold text-amber-900">{{ $pendingReviewCount }}</span>
                    @endif
                </a>
                <a href="{{ route('coach.projects.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 shadow-sm transition">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    + Buat Proyek
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            {{-- Ringkasan Metrik Coach --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-stat-card
                    title="Antrean Review Video"
                    :value="$pendingReviewCount"
                    :description="$pendingReviewCount > 0 ? 'Memerlukan tindakan review Anda' : 'Tidak ada antrean pending'"
                    color="amber"
                    :href="route('coach.videos.index', ['status' => 'pending'])"
                    aria-label="Antrean review: {{ $pendingReviewCount }} video menunggu review"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icon>
                </x-stat-card>

                <x-stat-card
                    title="Proyek Bimbingan"
                    :value="$projectStats['total']"
                    :description="$projectStats['active'] . ' aktif • ' . $projectStats['completed'] . ' selesai'"
                    color="blue"
                    :href="route('coach.projects.index')"
                    aria-label="Proyek bimbingan: {{ $projectStats['total'] }} proyek total"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </x-slot:icon>
                </x-stat-card>

                <x-stat-card
                    title="Total Tugas Mahasiswa"
                    :value="$taskStats['total']"
                    :description="$taskStats['todo'] . ' To Do • ' . $taskStats['in_progress'] . ' In Progress • ' . $taskStats['done'] . ' Done'"
                    color="emerald"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </x-slot:icon>
                </x-stat-card>

                <x-stat-card
                    title="Mahasiswa Bimbingan"
                    :value="$studentCount"
                    description="Peserta magang aktif"
                    color="purple"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </x-slot:icon>
                </x-stat-card>
            </div>

            <!-- Bagian 1: Antrean Video yang Perlu Direview (Diprioritaskan di atas) -->
            <x-card title="Antrean Video Menunggu Review" subtitle="Daftar kiriman video dokumentasi terbaru dari mahasiswa yang memerlukan review Anda">
                <x-slot:actions>
                    <a href="{{ route('coach.videos.index', ['status' => 'pending']) }}"
                       aria-label="Lihat semua antrean video dokumentasi"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:underline flex items-center gap-1">
                        Lihat Semua Antrean &rarr;
                    </a>
                </x-slot:actions>

                <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700/60 text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Judul Video</th>
                                <th class="py-3 px-4">Mahasiswa</th>
                                <th class="py-3 px-4">Tugas / Proyek</th>
                                <th class="py-3 px-4">Tanggal Kirim</th>
                                <th class="py-3 px-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @forelse ($pendingVideos as $video)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3.5 px-4 font-medium text-slate-900 dark:text-white">
                                        <div class="font-semibold">{{ $video->title }}</div>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 font-normal mt-0.5">
                                            Platform: <span class="capitalize">{{ str_replace('_', ' ', $video->platform?->value ?? '-') }}</span>
                                        </p>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300">
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $video->user ? $video->user->name : '-' }}</div>
                                        <div class="text-slate-600 dark:text-slate-400 font-mono">{{ $video->user ? ($video->user->nim ?? $video->user->email) : '' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300">
                                        <div class="font-medium text-slate-800 dark:text-slate-200">{{ $video->task ? $video->task->title : '-' }}</div>
                                        <div class="text-slate-600 dark:text-slate-400 text-[11px]">{{ $video->task && $video->task->project ? $video->task->project->name : '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 text-xs whitespace-nowrap">
                                        {{ $video->created_at->format('d M Y H:i') }} WIB
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <a href="{{ route('coach.videos.show', $video) }}"
                                           aria-label="Review video {{ $video->title }} oleh {{ $video->user ? $video->user->name : '-' }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg bg-amber-700 hover:bg-amber-800 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition">
                                            <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Review Sekarang →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-xs text-slate-600 dark:text-slate-400">
                                        <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-2" aria-hidden="true">
                                            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        Tidak ada video dokumentasi yang sedang menunggu review. Semua tugas terkendali!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Bagian 2: Proyek Bimbingan Terbaru -->
            <x-card title="Proyek Bimbingan Terbaru" subtitle="Proyek aktif dan selesai yang Anda kelola">
                <x-slot:actions>
                    <a href="{{ route('coach.projects.index') }}"
                       aria-label="Kelola semua proyek bimbingan magang"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 inline-flex items-center gap-1">
                        <span>Kelola Semua Proyek</span>
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </x-slot:actions>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                    @forelse ($recentProjects as $project)
                        <div class="rounded-xl border border-slate-200/90 dark:border-slate-700/80 p-5 sm:p-6 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col justify-between hover:border-blue-400 dark:hover:border-blue-500/80 shadow-sm hover:shadow-md transition-all group">
                            <div>
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/40 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                                        </svg>
                                    </div>
                                    <x-status-badge :status="$project->status" />
                                </div>
                                <h4 class="font-bold text-base text-slate-900 dark:text-white leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                    <a href="{{ route('coach.projects.show', $project) }}" class="focus:outline-none">
                                        {{ $project->name }}
                                    </a>
                                </h4>
                                <p class="mt-2 text-xs text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed min-h-[2.5rem]">
                                    {{ $project->description ?: 'Belum ada deskripsi proyek.' }}
                                </p>
                            </div>

                            <div class="mt-5 pt-3.5 border-t border-slate-200/80 dark:border-slate-700/60 flex flex-wrap items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 font-medium text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.765l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        {{ $project->members_count }} Mahasiswa
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 font-medium text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-slate-600 dark:text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        {{ $project->tasks_count }} Tugas
                                    </span>
                                </div>
                                <a href="{{ route('coach.projects.show', $project) }}"
                                   aria-label="Detail proyek {{ $project->name }}"
                                   class="inline-flex items-center gap-1 font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    <span>Detail Proyek</span>
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 py-8 text-center text-xs text-slate-600 dark:text-slate-400">
                            Belum ada proyek yang dibuat. Silakan klik tombol "+ Buat Proyek" di atas untuk memulai.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
