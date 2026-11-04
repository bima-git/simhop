<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Detail Tugas: {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Informasi Tugas -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 pb-4 dark:border-gray-700">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                            {{ $task->project ? $task->project->name : 'Proyek' }}
                        </span>
                        <h3 class="mt-1 text-2xl font-bold">{{ $task->title }}</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Status Tugas:</span>
                        <x-status-badge :status="$task->status" />
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Coach Pembimbing:</p>
                        <p class="font-medium">{{ $task->project && $task->project->creator ? $task->project->creator->name : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Tenggat Waktu:</p>
                        <p class="font-medium">{{ $task->due_date ? $task->due_date->format('d F Y') : 'Tidak ditentukan' }}</p>
                    </div>
                </div>

                <div class="mt-4 text-sm">
                    <p class="text-gray-500 dark:text-gray-400">Deskripsi Tugas:</p>
                    <p class="mt-1 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $task->description ?: 'Tidak ada deskripsi.' }}</p>
                </div>

                <div class="mt-6 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-500 dark:text-gray-400">Progres Pengerjaan</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $task->progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700 overflow-hidden">
                        <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300" style="width: {{ $task->progress }}%"></div>
                    </div>
                    @if ($task->progress_note)
                        <div class="mt-3 rounded-md bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-900/50 dark:text-gray-400">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Catatan Progres Terakhir:</span>
                            <p class="mt-0.5">{{ $task->progress_note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Perbarui Progres Tugas -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <h4 class="mb-4 text-lg font-semibold">Perbarui Progres Pengerjaan</h4>

                @if ($task->status === \App\Models\Task::STATUS_DONE)
                    <div class="rounded-md bg-green-50 p-4 border border-green-200 dark:bg-green-950/30 dark:border-green-800 text-sm text-green-800 dark:text-green-300">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Tugas Selesai (Done)</span>
                        </div>
                        <p class="mt-1 text-xs">Tugas ini telah disetujui dan berstatus selesai. Anda tidak dapat mengubah status maupun progres tugas ini lagi.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('mahasiswa.tasks.progress', $task) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="status" value="Status Pengerjaan" />
                                <select id="status" name="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="todo" @selected(old('status', $task->status) === 'todo')>To Do (Belum Dimulai)</option>
                                    <option value="in_progress" @selected(old('status', $task->status) === 'in_progress')>In Progress (Sedang Dikerjakan)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Status <em>Done</em> hanya diberikan secara otomatis saat video dokumentasi disetujui Coach.</p>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="progress" value="Progres (0 - 100%)" />
                                <x-text-input id="progress" name="progress" type="number" min="0" max="100"
                                              class="mt-1 block w-full"
                                              :value="old('progress', $task->progress)" required />
                                <x-input-error :messages="$errors->get('progress')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="progress_note" value="Catatan Progres (Opsional)" />
                            <textarea id="progress_note" name="progress_note" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                      placeholder="Contoh: Sudah menyelesaikan desain halaman login dan database seeder.">{{ old('progress_note', $task->progress_note) }}</textarea>
                            <x-input-error :messages="$errors->get('progress_note')" class="mt-2" />
                        </div>

                        <div class="pt-2">
                            <x-primary-button>Perbarui Progres</x-primary-button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Dokumen Handover Proyek -->
            @if ($task->project && $task->project->handoverDocuments->isNotEmpty())
                <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold">Dokumen Handover Proyek</h4>
                        <a href="{{ route('mahasiswa.projects.show', $task->project) }}"
                           class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                            Lihat Semua →
                        </a>
                    </div>

                    @php
                        $typeLabels = [
                            'guide' => 'Panduan (Guide)',
                            'tech_stack' => 'Tech Stack',
                            'migration' => 'Catatan Migrasi',
                            'asset' => 'Aset Pendukung',
                        ];
                    @endphp

                    @foreach ($task->project->handoverDocuments->groupBy('type') as $type => $docs)
                        <div class="mb-4 last:mb-0">
                            <h5 class="mb-2 text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $typeLabels[$type] ?? ucfirst($type) }}</h5>
                            <div class="space-y-2">
                                @foreach ($docs as $doc)
                                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-sm">{{ $doc->title }}</p>
                                                @if ($doc->content)
                                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 line-clamp-2">{{ $doc->content }}</p>
                                                @endif
                                            </div>
                                            @if ($doc->file_name)
                                                <a href="{{ route('handover.download', $doc) }}"
                                                   class="inline-flex items-center gap-1 shrink-0 rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">
                                                    📎 Unduh
                                                </a>
                                            @endif
                                        </div>
                                        @if ($doc->file_name)
                                            <p class="mt-1 text-xs text-gray-400">{{ $doc->file_name }} ({{ $doc->file_size }} KB)</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Bagian Video Dokumentasi & Review (T-04) -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold">Video Dokumentasi & Review</h4>
                    @if ($task->videoSubmissions->isNotEmpty())
                        <x-status-badge :status="$task->videoSubmissions->first()->status" />
                    @endif
                </div>

                @if ($task->videoSubmissions->isNotEmpty())
                    @php $video = $task->videoSubmissions->first(); @endphp
                    <div class="space-y-4">
                        @if ($video->embed_url)
                            <div class="mb-4 overflow-hidden rounded-lg">
                                <x-video-player :submission="$video" />
                            </div>
                        @endif

                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h5 class="font-medium text-base text-gray-900 dark:text-gray-100">{{ $video->title }}</h5>
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
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-3 whitespace-pre-line border-t border-gray-100 dark:border-gray-700/50 pt-2">{{ $video->description }}</p>
                            @endif
                        </div>

                        {{-- Riwayat Catatan Revisi --}}
                        @if ($video->revisions->isNotEmpty())
                            <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                <h5 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Riwayat Catatan Revisi Coach</h5>
                                <ul class="space-y-3">
                                    @foreach ($video->revisions as $rev)
                                        <li class="rounded-lg bg-orange-50 p-4 text-xs text-orange-900 dark:bg-orange-950/40 dark:text-orange-200 border border-orange-200 dark:border-orange-800">
                                            <div class="flex justify-between items-center font-semibold mb-1.5 pb-1 border-b border-orange-200/60 dark:border-orange-800/60">
                                                <span>👨‍🏫 {{ $rev->coach ? $rev->coach->name : 'Coach' }}</span>
                                                <span class="text-orange-700 dark:text-orange-400 font-normal">{{ $rev->created_at->format('d M Y H:i') }} WIB</span>
                                            </div>
                                            <p class="whitespace-pre-line leading-relaxed text-sm">{{ $rev->note }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Pemberitahuan status & Form Kirim Ulang bila Revision --}}
                        @if ($video->status === 'pending')
                            <div class="rounded-md bg-amber-50 p-4 border border-amber-200 dark:bg-amber-950/30 dark:border-amber-800 text-sm text-amber-800 dark:text-amber-300">
                                <div class="flex items-center gap-2 font-semibold">
                                    <span>⏳ Menunggu Review</span>
                                </div>
                                <p class="mt-1 text-xs">Video dokumentasi sedang menunggu review dan persetujuan dari Coach pembimbing.</p>
                            </div>
                        @elseif ($video->status === 'approved')
                            <div class="rounded-md bg-green-50 p-4 border border-green-200 dark:bg-green-950/30 dark:border-green-800 text-sm text-green-800 dark:text-green-300">
                                <div class="flex items-center gap-2 font-semibold">
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Video Disetujui (Approved)</span>
                                </div>
                                <p class="mt-1 text-xs">Video dokumentasi telah disetujui oleh Coach. Tugas telah selesai dikerjakan.</p>
                            </div>
                        @elseif ($video->status === 'revision')
                            <div class="rounded-md bg-orange-50 p-4 border border-orange-200 dark:bg-orange-950/30 dark:border-orange-800 text-sm text-orange-800 dark:text-orange-300">
                                <div class="flex items-center gap-2 font-semibold">
                                    <span>⚠️ Perlu Revisi</span>
                                </div>
                                <p class="mt-1 text-xs">Coach meminta perbaikan/revisi untuk video ini. Silakan pelajari catatan revisi di atas, perbaiki video Anda, lalu kirimkan tautan video perbaikan melalui form di bawah.</p>
                            </div>

                            @if ($task->status !== \App\Models\Task::STATUS_DONE)
                                <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                                    <h5 class="mb-4 font-semibold text-base text-gray-900 dark:text-gray-100">Kirim Ulang Video Dokumentasi</h5>
                                    <form method="POST" action="{{ route('mahasiswa.videos.update', $video) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <x-input-label for="resubmit_title" value="Judul Video" />
                                            <x-text-input id="resubmit_title" name="title" type="text" class="mt-1 block w-full"
                                                          :value="old('title', $video->title)" required />
                                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="resubmit_video_url" value="Tautan Video Baru (YouTube atau Google Drive)" />
                                            <x-text-input id="resubmit_video_url" name="video_url" type="url" class="mt-1 block w-full"
                                                          :value="old('video_url', $video->video_url)" required />
                                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                                                <span>• <strong>YouTube:</strong> Disarankan setelan privasi <em>Unlisted</em> (Tidak Publik).</span><br>
                                                <span>• <strong>Google Drive:</strong> Pastikan izin tautan disetel ke <em>"Siapa saja yang memiliki link"</em> (Viewer).</span>
                                            </p>
                                            <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="resubmit_description" value="Deskripsi / Catatan Perbaikan (Opsional)" />
                                            <textarea id="resubmit_description" name="description" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                                      placeholder="Jelaskan bagian apa yang telah diperbaiki sesuai catatan coach...">{{ old('description', $video->description) }}</textarea>
                                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                        </div>

                                        <x-primary-button>Kirim Ulang Video</x-primary-button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                @else
                    @if ($task->status === \App\Models\Task::STATUS_DONE)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Tugas ini telah selesai. Tidak ada video dokumentasi yang perlu dikirim.
                        </p>
                    @else
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Kirimkan tautan video dokumentasi hasil pengerjaan tugas Anda untuk direview oleh Coach pembimbing.
                        </p>

                        <form method="POST" action="{{ route('mahasiswa.videos.store', $task) }}" class="space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="create_title" value="Judul Video" />
                                <x-text-input id="create_title" name="title" type="text" class="mt-1 block w-full"
                                              :value="old('title', 'Dokumentasi: ' . $task->title)" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="create_video_url" value="Tautan Video (YouTube atau Google Drive)" />
                                <x-text-input id="create_video_url" name="video_url" type="url" class="mt-1 block w-full"
                                              :value="old('video_url')" placeholder="https://www.youtube.com/watch?v=... atau https://drive.google.com/file/d/.../view" required />
                                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                                    <span>• <strong>YouTube:</strong> Disarankan setelan privasi <em>Unlisted</em> (Tidak Publik).</span><br>
                                    <span>• <strong>Google Drive:</strong> Pastikan izin tautan disetel ke <em>"Siapa saja yang memiliki link"</em> (Viewer).</span>
                                </p>
                                <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="create_description" value="Deskripsi / Catatan Video (Opsional)" />
                                <textarea id="create_description" name="description" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                          placeholder="Jelaskan secara singkat apa yang didemonstrasikan dalam video ini...">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <x-primary-button>Kirim Video Dokumentasi</x-primary-button>
                        </form>
                    @endif
                @endif
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('mahasiswa.tasks.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                    ← Kembali ke Daftar Tugas
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('mahasiswa.dashboard') }}" class="text-sm text-gray-600 hover:underline dark:text-gray-400">
                    Dashboard Mahasiswa
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
