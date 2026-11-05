<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        {{ $project->name }}
                    </h2>
                    <x-status-badge :status="$project->status" />
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                    <span>Dibuat oleh Anda</span>
                    <span>&bull;</span>
                    @if ($project->start_date)
                        <span>{{ $project->start_date->format('d M Y') }}</span>
                    @endif
                    @if ($project->end_date)
                        <span>s.d. {{ $project->end_date->format('d M Y') }}</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('coach.handover.index', $project) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 dark:hover:bg-emerald-900/60 rounded-lg border border-emerald-200 dark:border-emerald-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Kelola Handover
                </a>
                <a href="{{ route('coach.projects.index') }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <!-- Project Overview Card -->
            <x-card>
                <div class="space-y-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Deskripsi Proyek</span>
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                        {{ $project->description ?: 'Belum ada deskripsi spesifik untuk proyek ini.' }}
                    </p>
                </div>
            </x-card>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Anggota Tim Mahasiswa -->
                <div class="space-y-6">
                    <x-card title="Anggota Tim Mahasiswa" subtitle="{{ $project->members->count() }} mahasiswa terdaftar">
                        <ul class="divide-y divide-slate-100 dark:divide-slate-700/60 mb-5">
                            @forelse ($project->members as $member)
                                <li class="py-3 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                                {{ $member->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 truncate">
                                                {{ $member->nim ? 'NIM: ' . $member->nim : $member->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('coach.projects.members.destroy', [$project, $member]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-medium px-2 py-1 rounded hover:bg-rose-50 transition" onclick="return confirm('Keluarkan mahasiswa dari tim proyek?')">
                                            Keluarkan
                                        </button>
                                    </form>
                                </li>
                            @empty
                                <li class="py-4 text-center text-xs text-slate-400">
                                    Belum ada mahasiswa dalam proyek ini.
                                </li>
                            @endforelse
                        </ul>

                        <!-- Form Tambah Anggota -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60">
                            @if ($students->isNotEmpty())
                                <form method="POST" action="{{ route('coach.projects.members.store', $project) }}" class="space-y-3">
                                    @csrf
                                    <div>
                                        <x-input-label for="user_id" value="Tambah Mahasiswa ke Proyek" class="text-xs font-semibold" />
                                        <select id="user_id" name="user_id" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-xs shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                            @foreach ($students as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nim ?? $s->email }})</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                                    </div>
                                    <x-primary-button class="w-full text-xs py-2">
                                        + Tambahkan Mahasiswa
                                    </x-primary-button>
                                </form>
                            @else
                                <p class="text-xs text-slate-400 text-center py-2">
                                    Semua mahasiswa aktif telah terdaftar pada proyek ini.
                                </p>
                            @endif
                        </div>
                    </x-card>
                </div>

                <!-- Right: Daftar Tugas Proyek -->
                <div class="lg:col-span-2 space-y-6">
                    <x-card title="Daftar Tugas Proyek" subtitle="{{ $project->tasks->count() }} tugas dibuat">
                        <div class="overflow-x-auto -mx-5 -my-5 sm:-mx-6 sm:-my-6">
                            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700/60 text-left text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="py-3 px-4">Tugas</th>
                                        <th class="py-3 px-4">Penerima</th>
                                        <th class="py-3 px-4">Tenggat</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4">Progres</th>
                                        <th class="py-3 px-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                    @forelse ($project->tasks as $task)
                                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                            <td class="py-3.5 px-4">
                                                <div class="font-medium text-slate-900 dark:text-white">
                                                    {{ $task->title }}
                                                </div>
                                                @if ($task->description)
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                                        {{ $task->description }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                                {{ $task->assignee ? $task->assignee->name : 'Belum ditugaskan' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                                {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <x-status-badge :status="$task->status" />
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-16 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $task->progress }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $task->progress }}%</span>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1.5">
                                                @if ($task->videoSubmissions->isNotEmpty())
                                                    <a href="{{ route('coach.videos.show', $task->videoSubmissions->first()) }}"
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                        Review Video
                                                    </a>
                                                @endif
                                                <a href="{{ route('coach.tasks.edit', $task) }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('coach.tasks.destroy', $task) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 dark:text-rose-400 font-medium px-2 py-1 rounded hover:bg-rose-50 transition"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini? Video dan revisi terkait juga akan dihapus.')">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                                                Belum ada tugas pada proyek ini. Buat tugas baru melalui formulir di bawah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>

                    <!-- Form Tambah Tugas Baru -->
                    <x-card title="Buat Tugas Baru" subtitle="Tetapkan rincian tugas untuk mahasiswa anggota proyek">
                        <form method="POST" action="{{ route('coach.projects.tasks.store', $project) }}" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="title" value="Judul Tugas" class="text-xs font-semibold" />
                                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full rounded-lg text-sm border-slate-300 dark:border-slate-700 shadow-xs focus:border-blue-500 focus:ring-blue-500"
                                              :value="old('title')" placeholder="Misal: Implementasi Autentikasi dan Middleware" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-1.5" />
                            </div>

                            <div>
                                <x-input-label for="description" value="Deskripsi Tugas" class="text-xs font-semibold" />
                                <textarea id="description" name="description" rows="3"
                                          class="mt-1 block w-full rounded-lg text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-900 shadow-xs focus:border-blue-500 focus:ring-blue-500" placeholder="Jelaskan kebutuhan teknis, batasan, dan kriteria tugas ini...">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="due_date" value="Tenggat Waktu" class="text-xs font-semibold" />
                                    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full rounded-lg text-sm border-slate-300 dark:border-slate-700 shadow-xs focus:border-blue-500 focus:ring-blue-500"
                                                  :value="old('due_date')" />
                                    <x-input-error :messages="$errors->get('due_date')" class="mt-1.5" />
                                </div>
                                <div>
                                    <x-input-label for="assigned_to" value="Tugaskan Kepada" class="text-xs font-semibold" />
                                    <select id="assigned_to" name="assigned_to"
                                            class="mt-1 block w-full rounded-lg text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-900 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">-- Belum Ditugaskan --</option>
                                        @foreach ($memberStudents as $ms)
                                            <option value="{{ $ms->id }}" @selected(old('assigned_to') == $ms->id)>
                                                {{ $ms->name }} ({{ $ms->nim ?? $ms->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('assigned_to')" class="mt-1.5" />
                                </div>
                            </div>

                            <div class="pt-2">
                                <x-primary-button>
                                    Simpan Tugas Baru
                                </x-primary-button>
                            </div>
                        </form>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
