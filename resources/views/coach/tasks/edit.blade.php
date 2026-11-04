<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Edit Tugas: {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                    Proyek: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $project->name }}</span>
                </p>

                <form method="POST" action="{{ route('coach.tasks.update', $task) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" value="Judul Tugas" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                      :value="old('title', $task->title)" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi Tugas" />
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description', $task->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="due_date" value="Tenggat Waktu" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
                                          :value="old('due_date', $task->due_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="assigned_to" value="Tugaskan Kepada" />
                            <select id="assigned_to" name="assigned_to"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">-- Belum Ditugaskan --</option>
                                @foreach ($memberStudents as $student)
                                    <option value="{{ $student->id }}" @selected(old('assigned_to', $task->assigned_to) == $student->id)>
                                        {{ $student->name }} ({{ $student->nim ?? $student->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                        <a href="{{ route('coach.projects.show', $project) }}" class="text-sm text-gray-600 hover:underline dark:text-gray-400">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
