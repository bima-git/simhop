<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-800 dark:text-slate-100">
            Review Video Dokumentasi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg bg-emerald-100 p-4 text-sm font-medium text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Tab Filter Status -->
            <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3 dark:border-slate-700">
                <a href="{{ route('coach.videos.index', ['status' => 'pending']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === 'pending' ? 'bg-amber-100 text-amber-900 font-semibold dark:bg-amber-900/60 dark:text-amber-200' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                    <span>⏳ Menunggu Review</span>
                    <span class="rounded-full bg-amber-200 px-2 py-0.5 text-xs text-amber-900 dark:bg-amber-800 dark:text-amber-100">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('coach.videos.index', ['status' => 'revision']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === 'revision' ? 'bg-orange-100 text-orange-900 font-semibold dark:bg-orange-900/60 dark:text-orange-200' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                    <span>⚠️ Perlu Revisi</span>
                    <span class="rounded-full bg-orange-200 px-2 py-0.5 text-xs text-orange-900 dark:bg-orange-800 dark:text-orange-100">{{ $counts['revision'] }}</span>
                </a>
                <a href="{{ route('coach.videos.index', ['status' => 'approved']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === 'approved' ? 'bg-emerald-100 text-emerald-900 font-semibold dark:bg-emerald-900/60 dark:text-emerald-200' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                    <span>✅ Disetujui</span>
                    <span class="rounded-full bg-emerald-200 px-2 py-0.5 text-xs text-emerald-900 dark:bg-emerald-800 dark:text-emerald-100">{{ $counts['approved'] }}</span>
                </a>
                <a href="{{ route('coach.videos.index', ['status' => 'all']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === 'all' ? 'bg-slate-200 text-slate-900 font-semibold dark:bg-slate-700 dark:text-slate-100' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                    <span>Semua Video</span>
                </a>
            </div>

            <!-- Tabel Antrean Video -->
            <div class="rounded-xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-700/80 dark:bg-slate-800 dark:text-slate-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                        <thead>
                            <tr class="text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                <th class="py-3 px-4">Judul Video</th>
                                <th class="py-3 px-4">Mahasiswa</th>
                                <th class="py-3 px-4">Tugas / Proyek</th>
                                <th class="py-3 px-4">Platform</th>
                                <th class="py-3 px-4">Tanggal Kirim</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse ($videos as $video)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-3 px-4 font-medium text-slate-900 dark:text-slate-100">
                                        {{ $video->title }}
                                        @if ($video->description)
                                            <p class="text-xs text-slate-600 dark:text-slate-400 font-normal mt-0.5 line-clamp-1">{{ $video->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $video->user ? $video->user->name : '-' }}</p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 font-mono">{{ $video->user ? ($video->user->nim ?? $video->user->email) : '' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $video->task ? $video->task->title : '-' }}</p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400">{{ $video->task && $video->task->project ? $video->task->project->name : '-' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300 capitalize text-xs">
                                        {{ str_replace('_', ' ', $video->platform?->value ?? '-') }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300 text-xs">
                                        {{ $video->created_at->format('d M Y H:i') }} WIB
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$video->status" />
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('coach.videos.show', $video) }}"
                                           class="inline-flex items-center rounded-lg bg-blue-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                                            Review Video →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 px-4 text-center text-slate-600 dark:text-slate-400">
                                        @if ($status === 'pending')
                                            Tidak ada video yang sedang menunggu review saat ini.
                                        @else
                                            Tidak ada data video untuk status ini.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($videos->hasPages())
                    <div class="mt-4 border-t border-slate-200 pt-4 dark:border-slate-700">
                        {{ $videos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
