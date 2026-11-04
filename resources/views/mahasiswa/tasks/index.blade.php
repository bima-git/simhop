<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Tugas Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                <th class="py-3 px-4">Judul Tugas</th>
                                <th class="py-3 px-4">Proyek</th>
                                <th class="py-3 px-4">Tenggat</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Progres</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($tasks as $task)
                                <tr>
                                    <td class="py-3 px-4 font-medium">
                                        {{ $task->title }}
                                        @if ($task->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 font-normal mt-0.5 line-clamp-1">{{ $task->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        {{ $task->project ? $task->project->name : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                        {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$task->status" />
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $task->progress }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $task->progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('mahasiswa.tasks.show', $task) }}" class="text-indigo-600 hover:underline dark:text-indigo-400 font-medium">
                                            Detail & Update
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 px-4 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada tugas yang ditugaskan kepada Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tasks->hasPages())
                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>

            <div>
                <a href="{{ route('mahasiswa.dashboard') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
