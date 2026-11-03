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

            <a href="{{ route('coach.projects.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali</a>
        </div>
    </div>
</x-app-layout>