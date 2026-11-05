<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('coach.handover.index', $document->project) }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Edit Dokumen: {{ $document->title }}
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                    Proyek: {{ $document->project->name }} &bull; Perbarui konten atau berkas lampiran dokumen
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="title">Perbarui Dokumen Handover</x-slot>
                <x-slot name="subtitle">Perubahan akan langsung terlihat oleh mahasiswa anggota proyek</x-slot>

                <form method="POST" action="{{ route('coach.handover.update', $document) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" value="Judul Dokumen" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                      :value="old('title', $document->title)" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Tipe Dokumen" />
                        <select id="type" name="type"
                                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            <option value="guide" @selected(old('type', $document->type) === 'guide')>Panduan (Guide)</option>
                            <option value="tech_stack" @selected(old('type', $document->type) === 'tech_stack')>Tech Stack</option>
                            <option value="migration" @selected(old('type', $document->type) === 'migration')>Catatan Migrasi</option>
                            <option value="asset" @selected(old('type', $document->type) === 'asset')>Aset Pendukung</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="content" value="Isi / Konten Teks Dokumentasi" />
                        <textarea id="content" name="content" rows="7"
                                  class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm font-mono text-xs focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400">{{ old('content', $document->content) }}</textarea>
                        <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">Dapat diisi instruksi teks, snippet perintah CLI, atau langkah deployment.</p>
                        <x-input-error :messages="$errors->get('content')" class="mt-1.5" />
                    </div>

                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                        <x-input-label for="file" value="Ganti Berkas Lampiran (Opsional)" />
                        @if ($document->file_name)
                            <div class="flex items-center gap-2 p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-mono">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.373L8.557 18.315a1.5 1.5 0 11-2.121-2.121l9.88-9.879" />
                                </svg>
                                <span class="text-slate-600 dark:text-slate-400">Berkas saat ini:</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $document->file_name }}</span>
                                <span class="text-slate-600 dark:text-slate-400">({{ $document->file_size }} KB)</span>
                            </div>
                        @endif
                        <input id="file" name="file" type="file"
                               class="mt-1 block w-full text-xs text-slate-600 dark:text-slate-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-xs file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100
                                      dark:file:bg-blue-950/60 dark:file:text-blue-300 cursor-pointer" />
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 font-mono">
                            Kosongkan jika tidak ingin mengganti berkas. Format didukung: pdf, zip, txt, md, doc, docx, png, jpg. Maksimal: 10240 KB.
                        </p>
                        <x-input-error :messages="$errors->get('file')" class="mt-1.5" />
                    </div>

                    <div class="flex items-center gap-3 pt-5 border-t border-slate-200 dark:border-slate-700/80">
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                        <a href="{{ route('coach.handover.index', $document->project) }}"
                           class="inline-flex items-center rounded-lg px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>