<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Review Video Dokumentasi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Tab Filter Status -->
            <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3 dark:border-gray-700">
                <a href="{{ route('coach.videos.index', ['status' => 'pending']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === 'pending' ? 'bg-amber-100 text-amber-800 font-semibold dark:bg-amber-900/60 dark:text-amber-200' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                    <span>⏳ Menunggu Review</span>
                    <span class="rounded-full bg-amber-200 px-2 py-0.5 text-xs text-amber-900 dark:bg-amber-800 dark:text-amber-100">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('coach.videos.index', ['status' => 'revision']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === 'revision' ? 'bg-orange-100 text-orange-800 font-semibold dark:bg-orange-900/60 dark:text-orange-200' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                    <span>⚠️ Perlu Revisi</span>
                    <span class="rounded-full bg-orange-200 px-2 py-0.5 text-xs text-orange-900 dark:bg-orange-800 dark:text-orange-100">{{ $counts['revision'] }}</span>
                </a>
                <a href="{{ route('coach.videos.index', ['status' => 'approved']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === 'approved' ? 'bg-green-100 text-green-800 font-semibold dark:bg-green-900/60 dark:text-green-200' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                    <span>✅ Disetujui</span>
                    <span class="rounded-full bg-green-200 px-2 py-0.5 text-xs text-green-900 dark:bg-green-800 dark:text-green-100">{{ $counts['approved'] }}</span>
                </a>
                <a href="{{ route('coach.videos.index', ['status' => 'all']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === 'all' ? 'bg-gray-200 text-gray-800 font-semibold dark:bg-gray-700 dark:text-gray-200' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                    <span>Semua Video</span>
                </a>
            </div>

            <!-- Tabel Antrean Video -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                <th class="py-3 px-4">Judul Video</th>
                                <th class="py-3 px-4">Mahasiswa</th>
                                <th class="py-3 px-4">Tugas / Proyek</th>
                                <th class="py-3 px-4">Platform</th>
                                <th class="py-3 px-4">Tanggal Kirim</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($videos as $video)
                                <tr>
                                    <td class="py-3 px-4 font-medium">
                                        {{ $video->title }}
                                        @if ($video->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 font-normal mt-0.5 line-clamp-1">{{ $video->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $video->user ? $video->user->name : '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $video->user ? ($video->user->nim ?? $video->user->email) : '' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        <p class="font-medium">{{ $video->task ? $video->task->title : '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $video->task && $video->task->project ? $video->task->project->name : '-' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300 capitalize text-xs">
                                        {{ str_replace('_', ' ', $video->platform?->value ?? '-') }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300 text-xs">
                                        {{ $video->created_at->format('d M Y H:i') }} WIB
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$video->status" />
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('coach.videos.show', $video) }}"
                                           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">
                                            Review Video →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
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
                    <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                        {{ $videos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
