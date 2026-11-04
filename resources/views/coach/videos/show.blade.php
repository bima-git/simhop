<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Review Video: {{ $video->title }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 dark:text-gray-400">Status Saat Ini:</span>
                <x-status-badge :status="$video->status" />
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom Kiri / Utama: Video Player & Deskripsi (2 kolom) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                        <!-- Video Player Embed -->
                        <div class="mb-4 overflow-hidden rounded-lg">
                            <x-video-player :submission="$video" />
                        </div>

                        <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4 dark:border-gray-700">
                            <div>
                                <h3 class="text-xl font-bold">{{ $video->title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    Platform: <span class="capitalize font-medium">{{ str_replace('_', ' ', $video->platform?->value ?? '-') }}</span> ·
                                    Dikirim: {{ $video->created_at->format('d M Y H:i') }} WIB
                                </p>
                            </div>
                            <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline dark:text-indigo-400 shrink-0 font-medium">
                                Buka Tautan Asli ↗
                            </a>
                        </div>

                        @if ($video->description)
                            <div class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                                <p class="text-xs font-semibold uppercase text-gray-500 mb-1">Deskripsi / Catatan Mahasiswa:</p>
                                <p class="whitespace-pre-line">{{ $video->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Riwayat Revisi Video -->
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                        <h4 class="text-lg font-semibold mb-4">Riwayat Catatan Revisi</h4>

                        @if ($video->revisions->isNotEmpty())
                            <div class="space-y-3">
                                @foreach ($video->revisions as $index => $rev)
                                    <div class="rounded-lg bg-orange-50 p-4 text-sm text-orange-900 dark:bg-orange-950/40 dark:text-orange-200 border border-orange-200 dark:border-orange-800">
                                        <div class="flex justify-between items-center font-semibold mb-2 pb-1.5 border-b border-orange-200/60 dark:border-orange-800/60">
                                            <span>#{{ $index + 1 }} · {{ $rev->coach ? $rev->coach->name : 'Coach' }}</span>
                                            <span class="text-xs text-orange-700 dark:text-orange-400 font-normal">{{ $rev->created_at->format('d M Y H:i') }} WIB</span>
                                        </div>
                                        <p class="whitespace-pre-line leading-relaxed">{{ $rev->note }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Belum ada catatan revisi untuk video ini.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Info Tugas & Form Tindakan Review (1 kolom) -->
                <div class="space-y-6">
                    <!-- Info Tugas & Mahasiswa -->
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                        <h4 class="text-base font-semibold mb-4 border-b border-gray-200 pb-2 dark:border-gray-700">Informasi Tugas</h4>

                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Judul Tugas:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $video->task?->title }}</p>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Proyek Magang:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('coach.projects.show', $video->task->project) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">
                                        {{ $video->task?->project?->name }}
                                    </a>
                                </p>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Mahasiswa Pembuat:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $video->user?->name }}</p>
                                <p class="text-xs text-gray-400">{{ $video->user?->nim ?? $video->user?->email }}</p>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Tenggat Waktu:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $video->task?->due_date ? $video->task->due_date->format('d M Y') : '-' }}</p>
                            </div>

                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Status Tugas:</span>
                                <div class="mt-1 flex items-center gap-2">
                                    <x-status-badge :status="$video->task?->status" />
                                    <span class="text-xs text-gray-500">({{ $video->task?->progress }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Aksi Review -->
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                        <h4 class="text-base font-semibold mb-4 border-b border-gray-200 pb-2 dark:border-gray-700">Tindakan Review</h4>

                        @if ($video->status === 'approved')
                            <div class="rounded-md bg-green-50 p-4 border border-green-200 dark:bg-green-950/30 dark:border-green-800 text-sm text-green-800 dark:text-green-300">
                                <div class="flex items-center gap-2 font-semibold">
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Video Telah Disetujui</span>
                                </div>
                                <p class="mt-2 text-xs leading-relaxed">
                                    Disetujui pada {{ $video->reviewed_at?->format('d M Y H:i') }} WIB
                                    @if ($video->reviewer) oleh {{ $video->reviewer->name }} @endif.
                                    Tugas terkait telah selesai (Done) dan tidak dapat diubah lagi.
                                </p>
                            </div>
                        @else
                            <div class="space-y-6">
                                <!-- Aksi 1: Setujui Video -->
                                <div class="rounded-lg border border-green-200 bg-green-50/50 p-4 dark:border-green-800 dark:bg-green-950/20">
                                    <h5 class="font-semibold text-green-900 dark:text-green-200 text-sm mb-1">Setujui Video (Approve)</h5>
                                    <p class="text-xs text-green-700 dark:text-green-400 mb-3">
                                        Status video akan menjadi <strong>Disetujui</strong> dan status tugas mahasiswa otomatis berubah menjadi <strong>Selesai (Done)</strong> dengan progres 100%.
                                    </p>
                                    <form method="POST" action="{{ route('coach.videos.approve', $video) }}">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menyetujui video dokumentasi ini? Status tugas akan otomatis selesai (Done 100%).')"
                                                class="w-full inline-flex justify-center items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                                            ✅ Setujui Video Ini
                                        </button>
                                    </form>
                                </div>

                                <!-- Aksi 2: Minta Revisi -->
                                <div class="rounded-lg border border-orange-200 bg-orange-50/50 p-4 dark:border-orange-800 dark:bg-orange-950/20">
                                    <h5 class="font-semibold text-orange-900 dark:text-orange-200 text-sm mb-1">Minta Revisi Video</h5>
                                    <p class="text-xs text-orange-700 dark:text-orange-400 mb-3">
                                        Tuliskan catatan perbaikan dengan jelas. Status video akan menjadi <strong>Perlu Revisi</strong> dan mahasiswa dapat mengirim ulang video perbaikan.
                                    </p>
                                    <form method="POST" action="{{ route('coach.videos.revise', $video) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <x-input-label for="note" value="Catatan Revisi" class="text-xs font-semibold text-orange-900 dark:text-orange-200" />
                                            <textarea id="note" name="note" rows="4"
                                                      class="mt-1 block w-full rounded-md border-orange-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-orange-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                                                      placeholder="Contoh: Suara video kurang jelas pada menit ke-2. Mohon demokan juga bagian validasi input..." required>{{ old('note') }}</textarea>
                                            <x-input-error :messages="$errors->get('note')" class="mt-1" />
                                        </div>

                                        <button type="submit"
                                                onclick="return confirm('Kirimkan catatan revisi ini ke mahasiswa?')"
                                                class="w-full inline-flex justify-center items-center rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition">
                                            ⚠️ Minta Revisi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Link Navigasi Kembali -->
                    <div class="flex flex-col gap-2 text-sm">
                        <a href="{{ route('coach.videos.index') }}" class="text-indigo-600 hover:underline dark:text-indigo-400">
                            ← Kembali ke Antrean Review Video
                        </a>
                        <a href="{{ route('coach.projects.show', $video->task->project) }}" class="text-gray-500 hover:underline dark:text-gray-400">
                            ← Kembali ke Proyek ({{ $video->task->project->name }})
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
