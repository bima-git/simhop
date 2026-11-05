<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Tugas Saya
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Daftar seluruh tugas magang yang ditugaskan kepada Anda beserta status penyelesaiannya
                </p>
            </div>
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <x-card>
                <div class="overflow-x-auto -mx-6 -my-4">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/80 text-left text-sm">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                            <tr class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <th scope="col" class="py-3 px-6">Judul Tugas</th>
                                <th scope="col" class="py-3 px-6">Proyek</th>
                                <th scope="col" class="py-3 px-6">Tenggat</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                                <th scope="col" class="py-3 px-6">Progres</th>
                                <th scope="col" class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($tasks as $task)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                            <a href="{{ route('mahasiswa.tasks.show', $task) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                                                {{ $task->title }}
                                            </a>
                                        </div>
                                        @if ($task->description)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-normal mt-0.5 line-clamp-1 max-w-md">
                                                {{ $task->description }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ $task->project ? $task->project->name : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-xs whitespace-nowrap">
                                        @if ($task->due_date)
                                            @if ($task->due_date->isPast() && $task->status !== 'done')
                                                <span class="inline-flex items-center gap-1 font-semibold text-rose-600 dark:text-rose-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $task->due_date->format('d M Y') }} (Terlewat)
                                                </span>
                                            @elseif ($task->due_date->isToday())
                                                <span class="inline-flex items-center gap-1 font-semibold text-amber-600 dark:text-amber-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Hari Ini ({{ $task->due_date->format('d M') }})
                                                </span>
                                            @else
                                                <span class="text-slate-600 dark:text-slate-300">{{ $task->due_date->format('d M Y') }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <x-status-badge :status="$task->status" />
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-24 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $task->progress }}%"></div>
                                            </div>
                                            <span class="font-mono text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $task->progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <a href="{{ route('mahasiswa.tasks.show', $task) }}"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            Detail & Update
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 px-6 text-center text-slate-500 dark:text-slate-400">
                                        Belum ada tugas yang ditugaskan kepada Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tasks->hasPages())
                    <x-slot name="footer">
                        {{ $tasks->links() }}
                    </x-slot>
                @endif
            </x-card>

            <div class="pt-2">
                <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>