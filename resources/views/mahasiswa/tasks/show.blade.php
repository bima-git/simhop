<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        {{ $task->title }}
                    </h2>
                    <x-status-badge :status="$task->status" />
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-2">
                    <span>Proyek: <strong class="text-slate-700 dark:text-slate-300">{{ $task->project ? $task->project->name : 'Proyek' }}</strong></span>
                    <span>&bull;</span>
                    <span>Coach: <strong class="text-slate-700 dark:text-slate-300">{{ $task->project && $task->project->creator ? $task->project->creator->name : '-' }}</strong></span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('mahasiswa.tasks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 rounded-lg border border-slate-300 dark:border-slate-700 shadow-sm transition">
                    &larr; Daftar Tugas
                </a>
                <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 dark:hover:bg-blue-900/60 rounded-lg border border-blue-200 dark:border-blue-800 transition">
                    Dasbor
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <!-- Kolom Utama Kiri (Urutan 1, 2, 3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Bagian 1: Rincian Tugas -->
                    <x-card title="Rincian Tugas">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 block mb-1">Deskripsi Tugas</span>
                            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                                {{ $task->description ?: 'Tidak ada deskripsi rincian tugas.' }}
                            </p>
                        </div>
                    </x-card>

                    <!-- Bagian 2: Progres Pengerjaan -->
                    <x-card title="Progres Pengerjaan" subtitle="Laporkan kemajuan tugas berkala kepada Coach">
                        <div class="space-y-5">
                            <!-- Progress Bar Saat Ini & Catatan Terakhir -->
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-medium text-slate-600 dark:text-slate-400">Progres Saat Ini</span>
                                    <span class="font-bold text-blue-600 dark:text-blue-400">{{ $task->progress }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $task->progress }}%"></div>
                                </div>
                                @if ($task->progress_note)
                                    <div class="mt-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 p-3 text-xs text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/60">
                                        <span class="font-semibold text-slate-700 dark:text-slate-200 block mb-0.5">Catatan Progres Terakhir:</span>
                                        <p>{{ $task->progress_note }}</p>
                                    </div>
                                @endif
                            </div>

                            @if ($task->status === \App\Models\Task::STATUS_DONE)
                                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60">
                                    <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 p-4 border border-emerald-200 dark:border-emerald-800/70 text-emerald-900 dark:text-emerald-200 flex items-start gap-3">
                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-xs leading-relaxed">
                                            <strong class="block font-semibold text-sm text-emerald-800 dark:text-emerald-300 mb-0.5">Tugas Telah Selesai (Done)</strong>
                                            Tugas ini telah disetujui Coach (Status: Selesai). Anda tidak dapat mengubah status maupun persentase progres tugas ini lagi.
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60">
                                    <form method="POST" action="{{ route('mahasiswa.tasks.progress', $task) }}" class="space-y-4">
                                        @csrf
                                        @method('PATCH')

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="status" value="Status Pengerjaan" class="text-xs font-semibold" />
                                                <select id="status" name="status" class="mt-1 block w-full rounded-lg text-sm border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="todo" @selected(old('status', $task->status) === 'todo')>Belum Dimulai (To Do)</option>
                                                    <option value="in_progress" @selected(old('status', $task->status) === 'in_progress')>Sedang Dikerjakan (In Progress)</option>
                                                </select>
                                                <p class="mt-1 text-[11px] text-slate-600 dark:text-slate-400">Status <em>Selesai (Done)</em> hanya terjadi otomatis saat video dokumentasi disetujui Coach.</p>
                                                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
                                            </div>

                                            <div>
                                                <x-input-label for="progress" value="Persentase Progres (0 - 100%)" class="text-xs font-semibold" />
                                                <x-text-input id="progress" name="progress" type="number" min="0" max="100" class="mt-1 block w-full text-sm dark:[color-scheme:dark]"
                                                              :value="old('progress', $task->progress)" required />
                                                <x-input-error :messages="$errors->get('progress')" class="mt-1.5" />
                                            </div>
                                        </div>

                                        <div>
                                            <x-input-label for="progress_note" value="Catatan Progres (Opsional)" class="text-xs font-semibold" />
                                            <textarea id="progress_note" name="progress_note" rows="3" class="mt-1 block w-full rounded-lg text-sm border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                      placeholder="Contoh: Sudah menyelesaikan implementasi rute dan pengujian database seeder...">{{ old('progress_note', $task->progress_note) }}</textarea>
                                            <x-input-error :messages="$errors->get('progress_note')" class="mt-1.5" />
                                        </div>

                                        <div class="pt-1">
                                            <x-primary-button>
                                                Perbarui Progres
                                            </x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </x-card>

                    <!-- Bagian 3: Video Dokumentasi & Review -->
                    <x-card title="Video Dokumentasi & Review">
                        @if ($task->videoSubmissions->isNotEmpty())
                            @php $video = $task->videoSubmissions->first(); @endphp
                            <div class="space-y-5">
                                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-3">
                                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Status Review Terkini:</span>
                                    <x-status-badge :status="$video->status" />
                                </div>

                                @if ($video->embed_url)
                                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/80 bg-black aspect-video shadow-sm">
                                        <x-video-player :submission="$video" />
                                    </div>
                                @endif

                                <div class="rounded-xl border border-slate-200 dark:border-slate-700/80 p-4 bg-slate-50/50 dark:bg-slate-800/40">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h5 class="font-semibold text-base text-slate-900 dark:text-white">{{ $video->title }}</h5>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-2">
                                                <span>Platform: <strong class="capitalize font-semibold text-slate-700 dark:text-slate-300">{{ str_replace('_', ' ', $video->platform?->value ?? '-') }}</strong></span>
                                                <span>&bull;</span>
                                                <span>Dikirim: {{ $video->created_at->format('d M Y H:i') }} WIB</span>
                                            </p>
                                        </div>
                                        <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 rounded-lg border border-blue-200 dark:border-blue-800 shrink-0 transition">
                                            <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Buka Tautan Asli
                                        </a>
                                    </div>
                                    @if ($video->description)
                                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-3 pt-3 border-t border-slate-200/80 dark:border-slate-700/60 whitespace-pre-line leading-relaxed">
                                            {{ $video->description }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Riwayat Catatan Revisi Coach -->
                                @if ($video->revisions->isNotEmpty())
                                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 block mb-3">Catatan Revisi dari Coach Pembimbing</span>
                                        <div class="relative pl-6 before:absolute before:top-2 before:bottom-2 before:left-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700 space-y-4">
                                            @foreach ($video->revisions as $index => $rev)
                                                <div class="relative">
                                                    <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2 border-white dark:border-slate-800 bg-amber-500 shadow-sm" aria-hidden="true"></div>
                                                    <div class="rounded-xl bg-amber-50/70 dark:bg-amber-950/30 p-4 text-xs text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-800/60 space-y-1.5">
                                                        <div class="flex justify-between items-center font-semibold border-b border-amber-200/50 dark:border-amber-800/40 pb-1.5">
                                                            <span>{{ $rev->coach ? $rev->coach->name : 'Coach' }}</span>
                                                            <span class="text-amber-700 dark:text-amber-400 font-normal">{{ $rev->created_at->format('d M Y H:i') }} WIB</span>
                                                        </div>
                                                        <p class="whitespace-pre-line leading-relaxed text-sm text-slate-800 dark:text-slate-200">{{ $rev->note }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Status Notice & Form Kirim Ulang -->
                                @if ($video->status === 'pending')
                                    <div class="rounded-xl bg-amber-50 dark:bg-amber-950/30 p-4 border border-amber-200 dark:border-amber-800/70 text-amber-900 dark:text-amber-200 flex items-start gap-3">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="text-xs leading-relaxed">
                                            <strong class="block font-semibold text-sm text-amber-800 dark:text-amber-300 mb-0.5">Menunggu Review</strong>
                                            Video dokumentasi telah berhasil dikirim dan sedang menunggu antrean peninjauan oleh Coach pembimbing.
                                        </div>
                                    </div>
                                @elseif ($video->status === 'approved')
                                    <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/30 p-4 border border-emerald-200 dark:border-emerald-800/70 text-emerald-900 dark:text-emerald-200 flex items-start gap-3">
                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-xs leading-relaxed">
                                            <strong class="block font-semibold text-sm text-emerald-800 dark:text-emerald-300 mb-0.5">Video Telah Disetujui (Approved)</strong>
                                            Dokumentasi video telah disetujui oleh Coach. Tugas ini telah rampung 100%.
                                        </div>
                                    </div>
                                @elseif ($video->status === 'revision')
                                    <div class="rounded-xl bg-orange-50 dark:bg-orange-950/30 p-4 border border-orange-200 dark:border-orange-800/70 text-orange-900 dark:text-orange-200 flex items-start gap-3">
                                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 shrink-0 mt-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <div class="text-xs leading-relaxed">
                                            <strong class="block font-semibold text-sm text-orange-800 dark:text-orange-300 mb-0.5">Perlu Revisi</strong>
                                            Coach meminta perbaikan pada video dokumentasi Anda. Silakan pelajari catatan revisi di atas, perbaiki video Anda, dan kirimkan tautan perbaikan melalui form di bawah.
                                        </div>
                                    </div>

                                    @if ($task->status !== \App\Models\Task::STATUS_DONE)
                                        <div class="pt-5 border-t border-slate-100 dark:border-slate-700/60">
                                            <h5 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Kirim Ulang Video Dokumentasi</h5>
                                            <form method="POST" action="{{ route('mahasiswa.videos.update', $video) }}" class="space-y-4">
                                                @csrf
                                                @method('PUT')

                                                <div>
                                                    <x-input-label for="resubmit_title" value="Judul Video" class="text-xs font-semibold" />
                                                    <x-text-input id="resubmit_title" name="title" type="text" class="mt-1 block w-full text-sm"
                                                                  :value="old('title', $video->title)" required />
                                                    <x-input-error :messages="$errors->get('title')" class="mt-1.5" />
                                                </div>

                                                <div>
                                                    <x-input-label for="resubmit_video_url" value="Tautan Video Baru (YouTube atau Google Drive)" class="text-xs font-semibold" />
                                                    <x-text-input id="resubmit_video_url" name="video_url" type="url" class="mt-1 block w-full text-sm font-mono"
                                                                  :value="old('video_url', $video->video_url)" required />
                                                    <div class="mt-1.5 text-[11px] text-slate-600 dark:text-slate-400 space-y-0.5">
                                                        <p>&bull; <strong>YouTube:</strong> Gunakan opsi visibilitas <em>Unlisted</em> (Tidak Publik).</p>
                                                        <p>&bull; <strong>Google Drive:</strong> Pastikan izin akses disetel ke <em>"Siapa saja yang memiliki link"</em> (Viewer).</p>
                                                    </div>
                                                    <x-input-error :messages="$errors->get('video_url')" class="mt-1.5" />
                                                </div>

                                                <div>
                                                    <x-input-label for="resubmit_description" value="Deskripsi / Catatan Perbaikan (Opsional)" class="text-xs font-semibold" />
                                                    <textarea id="resubmit_description" name="description" rows="3"
                                                              class="mt-1 block w-full rounded-lg text-sm border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                              placeholder="Jelaskan secara ringkas bagian mana yang telah Anda perbaiki sesuai arahan Coach...">{{ old('description', $video->description) }}</textarea>
                                                    <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                                                </div>

                                                <div class="pt-1">
                                                    <x-primary-button>
                                                        Kirim Ulang Video
                                                    </x-primary-button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @else
                            @if ($task->status === \App\Models\Task::STATUS_DONE)
                                <p class="text-xs text-slate-600 dark:text-slate-400 py-3 text-center">
                                    Tugas ini telah selesai dikerjakan. Tidak ada video dokumentasi yang perlu dikirimkan.
                                </p>
                            @else
                                <div class="space-y-4">
                                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                        Kirimkan tautan video rekaman demonstrasi hasil pengerjaan tugas Anda untuk direview dan disetujui oleh Coach pembimbing.
                                    </p>

                                    <form method="POST" action="{{ route('mahasiswa.videos.store', $task) }}" class="space-y-4">
                                        @csrf

                                        <div>
                                            <x-input-label for="create_title" value="Judul Video" class="text-xs font-semibold" />
                                            <x-text-input id="create_title" name="title" type="text" class="mt-1 block w-full text-sm"
                                                          :value="old('title', 'Dokumentasi: ' . $task->title)" required />
                                            <x-input-error :messages="$errors->get('title')" class="mt-1.5" />
                                        </div>

                                        <div>
                                            <x-input-label for="create_video_url" value="Tautan Video (YouTube atau Google Drive)" class="text-xs font-semibold" />
                                            <x-text-input id="create_video_url" name="video_url" type="url" class="mt-1 block w-full text-sm font-mono"
                                                          :value="old('video_url')" placeholder="https://www.youtube.com/watch?v=... atau https://drive.google.com/file/d/.../view" required />
                                            <div class="mt-1.5 text-[11px] text-slate-600 dark:text-slate-400 space-y-0.5">
                                                <p>&bull; <strong>YouTube:</strong> Gunakan opsi visibilitas <em>Unlisted</em> (Tidak Publik).</p>
                                                <p>&bull; <strong>Google Drive:</strong> Pastikan izin akses disetel ke <em>"Siapa saja yang memiliki link"</em> (Viewer).</p>
                                            </div>
                                            <x-input-error :messages="$errors->get('video_url')" class="mt-1.5" />
                                        </div>

                                        <div>
                                            <x-input-label for="create_description" value="Deskripsi / Catatan Video (Opsional)" class="text-xs font-semibold" />
                                            <textarea id="create_description" name="description" rows="3"
                                                      class="mt-1 block w-full rounded-lg text-sm border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                      placeholder="Jelaskan secara singkat fitur dan demonstrasi yang ada dalam video ini...">{{ old('description') }}</textarea>
                                            <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                                        </div>

                                        <div class="pt-1">
                                            <x-primary-button>
                                                Kirim Video Dokumentasi
                                            </x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </x-card>
                </div>

                <!-- Kolom Samping Kanan (Sidebar: Status Metadata & Bagian 4: Dokumen Handover) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Status & Metadata Tugas -->
                    <x-card title="Status & Metadata">
                        <div class="space-y-4 text-xs">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700/60">
                                <span class="text-slate-600 dark:text-slate-400 font-medium">Status Tugas</span>
                                <x-status-badge :status="$task->status" />
                            </div>

                            <div class="pb-3 border-b border-slate-100 dark:border-slate-700/60">
                                <span class="text-slate-600 dark:text-slate-400 font-medium block mb-0.5">Proyek</span>
                                @if ($task->project)
                                    <a href="{{ route('mahasiswa.projects.show', [$task->project, 'from_task' => $task->id]) }}" class="font-semibold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition">
                                        {{ $task->project->name }}
                                    </a>
                                @else
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">-</span>
                                @endif
                            </div>

                            <div class="pb-3 border-b border-slate-100 dark:border-slate-700/60">
                                <span class="text-slate-600 dark:text-slate-400 font-medium block mb-0.5">Coach Pembimbing</span>
                                <span class="font-semibold text-slate-900 dark:text-white">
                                    {{ $task->project && $task->project->creator ? $task->project->creator->name : '-' }}
                                </span>
                            </div>

                            <div class="pb-3 border-b border-slate-100 dark:border-slate-700/60">
                                <span class="text-slate-600 dark:text-slate-400 font-medium block mb-0.5">Tenggat Waktu</span>
                                @if ($task->due_date)
                                    @if ($task->due_date->isPast() && $task->status !== \App\Models\Task::STATUS_DONE)
                                        <span class="font-semibold text-rose-600 dark:text-rose-400 inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $task->due_date->format('d F Y') }} (Terlewat)
                                        </span>
                                    @elseif ($task->due_date->isToday())
                                        <span class="font-semibold text-amber-600 dark:text-amber-400 inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Hari Ini ({{ $task->due_date->format('d M Y') }})
                                        </span>
                                    @else
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $task->due_date->format('d F Y') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="font-medium text-slate-600 dark:text-slate-400">Tidak ditentukan</span>
                                @endif
                            </div>

                            <div>
                                <span class="text-slate-600 dark:text-slate-400 font-medium block mb-0.5">Tanggal Dibuat</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $task->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </x-card>

                    <!-- Bagian 4: Dokumen Handover Proyek -->
                    <x-card title="Dokumen Handover">
                        @if ($task->project)
                            <x-slot:actions>
                                <a href="{{ route('mahasiswa.projects.show', [$task->project, 'from_task' => $task->id]) }}"
                                   aria-label="Lihat semua dokumen handover proyek {{ $task->project->name }}"
                                   class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:underline">
                                    Lihat Semua &rarr;
                                </a>
                            </x-slot:actions>
                        @endif

                        @if ($task->project && $task->project->handoverDocuments->isNotEmpty())
                            @php
                                $typeLabels = [
                                    'guide' => 'Panduan (Guide)',
                                    'tech_stack' => 'Tech Stack',
                                    'migration' => 'Migrasi & DB',
                                    'asset' => 'Aset Pendukung',
                                ];
                            @endphp

                            <div class="space-y-3">
                                @foreach ($task->project->handoverDocuments as $doc)
                                    <div class="rounded-xl border border-slate-200 dark:border-slate-700/80 p-3 bg-slate-50/50 dark:bg-slate-800/40 space-y-2">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 mb-1">
                                                    <x-status-badge :status="$doc->type" />
                                                </div>
                                                <h5 class="font-semibold text-xs text-slate-900 dark:text-white leading-snug truncate" title="{{ $doc->title }}">
                                                    {{ $doc->title }}
                                                </h5>
                                            </div>
                                        </div>

                                        @if ($doc->content)
                                            <p class="text-[11px] text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                                {{ $doc->content }}
                                            </p>
                                        @endif

                                        @if ($doc->file_name)
                                            <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-2 text-[11px]">
                                                <span class="font-mono text-slate-600 dark:text-slate-400 truncate max-w-[130px]" title="{{ $doc->file_name }}">
                                                    {{ $doc->file_name }}
                                                </span>
                                                <a href="{{ route('handover.download', $doc) }}"
                                                   aria-label="Unduh berkas {{ $doc->title }}"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-md shadow-sm shrink-0 transition">
                                                    <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Unduh Berkas
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-600 dark:text-slate-400 py-3 text-center">
                                Belum ada dokumen handover yang diunggah untuk proyek ini.
                            </p>
                        @endif
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
