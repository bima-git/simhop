<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('coach.projects.index') }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Proyek Baru
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Buat inisiasi proyek bimbingan magang baru
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="title">Informasi Proyek Magang</x-slot>
                <x-slot name="subtitle">Isi rincian nama, deskripsi, dan periode pelaksanaan proyek</x-slot>

                <form method="POST" action="{{ route('coach.projects.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nama Proyek" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      :value="old('name')" required autofocus placeholder="Contoh: Sistem Otomasi Server & Monitoring" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi Proyek" />
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500"
                                  placeholder="Jelaskan ruang lingkup, latar belakang, dan target proyek magang ini...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="start_date" value="Tanggal Mulai" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full dark:[color-scheme:dark]"
                                          :value="old('start_date')" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-1.5" />
                        </div>
                        <div>
                            <x-input-label for="end_date" value="Tanggal Selesai" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full dark:[color-scheme:dark]"
                                          :value="old('end_date')" />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-1.5" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-5 border-t border-slate-200 dark:border-slate-700/80">
                        <x-primary-button>Simpan</x-primary-button>
                        <a href="{{ route('coach.projects.index') }}"
                           class="inline-flex items-center rounded-lg px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>