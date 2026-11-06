<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Review Video: {{ $video->title }}
                    </h2>
                    <x-status-badge :status="$video->status" />
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-2">
                    <span>Tugas: <strong class="text-slate-700 dark:text-slate-300">{{ $video->task?->title }}</strong></span>
                    <span>&bull;</span>
                    <span>Mahasiswa: <strong class="text-slate-700 dark:text-slate-300">{{ $video->user?->name }}</strong></span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('coach.videos.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 rounded-lg border border-slate-300 dark:border-slate-600 transition">
                    &larr; Antrean Review
                </a>
                <a href="{{ route('coach.projects.show', $video->task->project) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 rounded-lg border border-blue-200 dark:border-blue-800 transition">
                    Buka Proyek
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <!-- Kolom Kiri: Player Video & Riwayat Revisi (2 kolom) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Video Player Card -->
                    <x-card>
                        <div class="mb-5 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/80 bg-black aspect-video shadow-sm">
                            <x-video-player :submission="$video" />
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-slate-100 dark:border-slate-700/60 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $video->title }}</h3>
                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-2">
                                    <span>Platform: <strong class="capitalize font-semibold text-slate-700 dark:text-slate-300">{{ str_replace('_', ' ', $video->platform?->value ?? '-') }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Dikirim: {{ $video->created_at->format('d M Y H:i') }} WIB</span>
                                </p>
                            </div>
                            <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 rounded-lg border border-blue-200 dark:border-blue-800 shrink-0 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Buka Tautan Asli
                            </a>
                        </div>

                        @if ($video->description)
                            <div class="mt-4 bg-slate-50 dark:bg-slate-800/60 rounded-lg p-4 border border-slate-200/80 dark:border-slate-700/50">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 block mb-1">Catatan dari Mahasiswa:</span>
                                <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                                    {{ $video->description }}
                                </p>
                            </div>
                        @endif
                    </x-card>

                    <!-- Riwayat Revisi Video (Vertical Timeline) -->
                    <x-card title="Riwayat Catatan Revisi" subtitle="Linimasa koreksi dan catatan review kronologis">
                        @if ($video->revisions->isNotEmpty())
                            <div class="relative pl-6 before:absolute before:top-2 before:bottom-2 before:left-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700 space-y-6">
                                @foreach ($video->revisions as $index => $rev)
                                    <div class="relative">
                                        <!-- Timeline node circle -->
                                        <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2 border-white dark:border-slate-800 bg-amber-500 shadow-sm"></div>

                                        <div class="bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-xl p-4 space-y-2">
                                            <div class="flex items-center justify-between text-xs border-b border-amber-200/50 dark:border-amber-800/40 pb-2">
                                                <div class="flex items-center gap-1.5 font-semibold text-amber-900 dark:text-amber-200">
                                                    <span class="px-1.5 py-0.5 rounded bg-amber-200/70 dark:bg-amber-900 text-[10px] font-bold">#{{ $index + 1 }}</span>
                                                    <span>{{ $rev->coach ? $rev->coach->name : 'Coach' }}</span>
                                                </div>
                                                <span class="text-amber-700 dark:text-amber-400 font-medium">
                                                    {{ $rev->created_at->format('d M Y H:i') }} WIB
                                                </span>
                                            </div>
                                            <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line leading-relaxed">
                                                {{ $rev->note }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700/60 text-slate-500 dark:text-slate-400 flex items-center justify-center mx-auto mb-2" aria-hidden="true">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-400">
                                    Belum ada catatan revisi pada video ini.
                                </p>
                            </div>
                        @endif
                    </x-card>
                </div>

                <!-- Kolom Kanan: Info Tugas & Panel Tindakan Review (1 kolom) -->
                <div class="space-y-6">
                    <!-- Info Tugas Card -->
                    <x-card title="Informasi Tugas">
                        <dl class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs">
                            <div class="py-2.5">
                                <dt class="text-slate-600 dark:text-slate-400 font-medium">Judul Tugas</dt>
                                <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-0.5">{{ $video->task?->title }}</dd>
                            </div>
                            <div class="py-2.5">
                                <dt class="text-slate-600 dark:text-slate-400 font-medium">Proyek Magang</dt>
                                <dd class="text-sm font-semibold text-blue-600 dark:text-blue-400 mt-0.5">
                                    <a href="{{ route('coach.projects.show', $video->task->project) }}" class="hover:underline">
                                        {{ $video->task?->project?->name }}
                                    </a>
                                </dd>
                            </div>
                            <div class="py-2.5">
                                <dt class="text-slate-600 dark:text-slate-400 font-medium">Mahasiswa Pembuat</dt>
                                <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-0.5">{{ $video->user?->name }}</dd>
                                <dd class="text-slate-600 dark:text-slate-400 font-mono">{{ $video->user?->nim ?? $video->user?->email }}</dd>
                            </div>
                            <div class="py-2.5">
                                <dt class="text-slate-600 dark:text-slate-400 font-medium">Tenggat Waktu</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                    {{ $video->task?->due_date ? $video->task->due_date->format('d M Y') : 'Tidak ada tenggat' }}
                                </dd>
                            </div>
                            <div class="py-2.5">
                                <dt class="text-slate-600 dark:text-slate-400 font-medium">Status Tugas Saat Ini</dt>
                                <dd class="mt-1 flex items-center gap-2">
                                    <x-status-badge :status="$video->task?->status" />
                                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">({{ $video->task?->progress }}%)</span>
                                </dd>
                            </div>
                        </dl>
                    </x-card>

                    <!-- Panel Tindakan Review -->
                    <x-card title="Tindakan Review">
                        @if ($video->status === 'approved')
                            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 p-4 border border-emerald-200 dark:border-emerald-800/70 text-emerald-900 dark:text-emerald-200">
                                <div class="flex items-center gap-2 font-bold text-sm text-emerald-800 dark:text-emerald-300">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Video Telah Disetujui</span>
                                </div>
                                <p class="mt-2 text-xs text-emerald-700 dark:text-emerald-300 leading-relaxed">
                                    Disetujui pada <strong>{{ $video->reviewed_at?->format('d M Y H:i') }} WIB</strong>
                                    @if ($video->reviewer) oleh {{ $video->reviewer->name }} @endif.
                                    Tugas telah otomatis ditandai selesai (Done, 100%).
                                </p>
                            </div>
                        @else
                            <div class="space-y-5">
                                <!-- Aksi 1: Setujui Video -->
                                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800/70 bg-emerald-50/50 dark:bg-emerald-950/20 p-4 space-y-2">
                                    <div class="flex items-center gap-1.5 text-emerald-900 dark:text-emerald-200 text-sm font-bold">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <h5>Setujui Video (Approve)</h5>
                                    </div>
                                    <p class="text-xs text-emerald-800 dark:text-emerald-300 leading-relaxed">
                                        Video akan disetujui dan status tugas mahasiswa otomatis tuntas <strong>Selesai (Done 100%)</strong>.
                                    </p>
                                    <form method="POST" action="{{ route('coach.videos.approve', $video) }}" class="pt-2">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menyetujui video dokumentasi ini? Status tugas akan otomatis selesai (Done 100%).')"
                                                class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition">
                                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Setujui Video Ini
                                        </button>
                                    </form>
                                </div>

                                <!-- Aksi 2: Minta Revisi -->
                                <div class="rounded-xl border border-amber-200 dark:border-amber-800/70 bg-amber-50/50 dark:bg-amber-950/20 p-4 space-y-2">
                                    <div class="flex items-center gap-1.5 text-amber-900 dark:text-amber-200 text-sm font-bold">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <h5>Minta Revisi Video</h5>
                                    </div>
                                    <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                                        Status video akan beralih ke <strong>Perlu Revisi</strong> agar mahasiswa dapat mengirimkan perbaikan.
                                    </p>
                                    <form method="POST" action="{{ route('coach.videos.revise', $video) }}" class="space-y-3 pt-2">
                                        @csrf
                                        <div>
                                            <x-input-label for="note" value="Catatan Revisi" class="text-xs font-semibold text-amber-900 dark:text-amber-200" />
                                            <textarea id="note" name="note" rows="4"
                                                      class="mt-1 block w-full rounded-lg border-amber-300 dark:border-amber-700 dark:bg-slate-900 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                                      placeholder="Contoh: Suara video kurang jelas pada menit ke-2. Mohon demokan juga bagian validasi input..." required>{{ old('note') }}</textarea>
                                            <x-input-error :messages="$errors->get('note')" class="mt-1" />
                                        </div>

                                        <button type="submit"
                                                onclick="return confirm('Kirimkan catatan revisi ini ke mahasiswa?')"
                                                class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-amber-700 hover:bg-amber-800 active:bg-amber-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition">
                                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Minta Revisi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
