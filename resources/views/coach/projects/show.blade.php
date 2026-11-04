<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ $project->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <p class="text-sm text-gray-500">Status: {{ $project->status }}
                    @if ($project->start_date) · {{ $project->start_date->format('d M Y') }} @endif
                    @if ($project->end_date) – {{ $project->end_date->format('d M Y') }} @endif
                </p>
                <p class="mt-2">{{ $project->description ?: 'Tidak ada deskripsi.' }}</p>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <h3 class="mb-3 font-semibold">Anggota Proyek</h3>

                <ul class="mb-4 divide-y text-sm">
                    @forelse ($project->members as $member)
                        <li class="flex items-center justify-between py-2">
                            <span>{{ $member->name }} <span class="text-gray-500">({{ $member->role }})</span></span>
                            <form method="POST"
                                  action="{{ route('coach.projects.members.destroy', [$project, $member]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline"
                                        onclick="return confirm('Keluarkan dari proyek?')">Keluarkan</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-gray-500">Belum ada anggota.</li>
                    @endforelse
                </ul>

                @if ($students->isNotEmpty())
                    <form method="POST" action="{{ route('coach.projects.members.store', $project) }}"
                          class="flex items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <x-input-label for="user_id" value="Tambah Mahasiswa" />
                            <select id="user_id" name="user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @foreach ($students as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>
                        <x-primary-button>Tambah</x-primary-button>
                    </form>
                @else
                    <p class="text-sm text-gray-500">Tidak ada mahasiswa aktif lain yang bisa ditambahkan.</p>
                @endif
            </div>

            <!-- Bagian Tugas Proyek -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <h3 class="mb-4 text-lg font-semibold">Tugas Proyek</h3>

                <div class="overflow-x-auto mb-8">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                <th class="py-3 px-4">Judul</th>
                                <th class="py-3 px-4">Penerima</th>
                                <th class="py-3 px-4">Tenggat</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Progres</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($project->tasks as $task)
                                <tr>
                                    <td class="py-3 px-4 font-medium">
                                        {{ $task->title }}
                                        @if ($task->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 font-normal mt-0.5 line-clamp-1">{{ $task->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        {{ $task->assignee ? $task->assignee->name : 'Belum ditugaskan' }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$task->status" />
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        {{ $task->progress }}%
                                    </td>
                                    <td class="py-3 px-4 text-right space-x-2">
                                        @if ($task->videoSubmissions->isNotEmpty())
                                            <a href="{{ route('coach.videos.show', $task->videoSubmissions->first()) }}"
                                               class="inline-flex items-center rounded bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:text-indigo-300">
                                                🎬 Review Video
                                            </a>
                                        @endif
                                        <a href="{{ route('coach.tasks.edit', $task) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">Edit</a>
                                        <form method="POST" action="{{ route('coach.tasks.destroy', $task) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline dark:text-red-400"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini? Video dan revisi terkait juga akan dihapus.')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">Belum ada tugas pada proyek ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Form Tambah Tugas Baru -->
                <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                    <h4 class="mb-4 font-semibold text-md">Tambah Tugas Baru</h4>
                    <form method="POST" action="{{ route('coach.projects.tasks.store', $project) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="title" value="Judul Tugas" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                          :value="old('title')" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Deskripsi Tugas" />
                            <textarea id="description" name="description" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="due_date" value="Tenggat Waktu" />
                                <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
                                              :value="old('due_date')" />
                                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="assigned_to" value="Tugaskan Kepada" />
                                <select id="assigned_to" name="assigned_to"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="">-- Belum Ditugaskan --</option>
                                    @foreach ($memberStudents as $ms)
                                        <option value="{{ $ms->id }}" @selected(old('assigned_to') == $ms->id)>
                                            {{ $ms->name }} ({{ $ms->nim ?? $ms->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                            </div>
                        </div>

                        <x-primary-button>Tambah Tugas</x-primary-button>
                    </form>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('coach.handover.index', $project) }}"
                   class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                    📄 Kelola Handover
                </a>
                <a href="{{ route('coach.projects.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali ke Daftar Proyek</a>
            </div>
        </div>
    </div>
</x-app-layout>