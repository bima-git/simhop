<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('coach.projects.show', $project) }}"
               aria-label="Kembali ke detail proyek"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Edit Tugas: {{ $task->title }}
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                    Proyek: {{ $project->name }} &bull; Perbarui deskripsi, tenggat waktu, atau penugasan
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <x-card>
                <x-slot name="title">Informasi Tugas</x-slot>
                <x-slot name="subtitle">Perubahan akan langsung terlihat oleh mahasiswa yang ditugaskan</x-slot>

                <form method="POST" action="{{ route('coach.tasks.update', $task) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" value="Judul Tugas" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                      :value="old('title', $task->title)" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi Tugas" />
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400"
                                  placeholder="Jelaskan kebutuhan teknis dan batasan tugas...">{{ old('description', $task->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="due_date" value="Tenggat Waktu" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full dark:[color-scheme:dark]"
                                          :value="old('due_date', $task->due_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-1.5" />
                        </div>
                        <div>
                            <x-input-label for="assigned_to" value="Tugaskan Kepada" />
                            <select id="assigned_to" name="assigned_to"
                                    class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">-- Belum Ditugaskan --</option>
                                @foreach ($memberStudents as $student)
                                    <option value="{{ $student->id }}" @selected(old('assigned_to', $task->assigned_to) == $student->id)>
                                        {{ $student->name }} ({{ $student->nim ?? $student->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('assigned_to')" class="mt-1.5" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-5 border-t border-slate-200 dark:border-slate-700/80">
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                        <a href="{{ route('coach.projects.show', $project) }}"
                           class="inline-flex items-center rounded-lg px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
