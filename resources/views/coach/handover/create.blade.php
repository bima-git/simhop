<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Tambah Dokumen Handover: {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <form method="POST" action="{{ route('coach.handover.store', $project) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="title" value="Judul Dokumen" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                      :value="old('title')" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Tipe Dokumen" />
                        <select id="type" name="type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="guide" @selected(old('type') === 'guide')>Panduan (Guide)</option>
                            <option value="tech_stack" @selected(old('type') === 'tech_stack')>Tech Stack</option>
                            <option value="migration" @selected(old('type') === 'migration')>Catatan Migrasi</option>
                            <option value="asset" @selected(old('type') === 'asset')>Aset Pendukung</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="content" value="Isi / Konten Teks" />
                        <textarea id="content" name="content" rows="6"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                  placeholder="Tulis panduan, spesifikasi tech stack, atau catatan migrasi di sini...">{{ old('content') }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="file" value="Berkas Lampiran (Opsional, maks 10 MB)" />
                        <input id="file" name="file" type="file"
                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100
                                      dark:file:bg-indigo-900 dark:file:text-indigo-300" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: pdf, zip, txt, md, doc, docx, png, jpg. Maksimal 10 MB.</p>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <x-primary-button>Simpan</x-primary-button>
                        <a href="{{ route('coach.handover.index', $project) }}" class="text-sm text-gray-600 hover:underline dark:text-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
