<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Proyek Saya</h2>
            <a href="{{ route('coach.projects.create') }}"
               class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                Proyek Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nama</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Anggota</th>
                            <th class="py-2">Tugas</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr class="border-b">
                                <td class="py-2">{{ $project->name }}</td>
                                <td class="py-2">{{ $project->status }}</td>
                                <td class="py-2">{{ $project->members_count }}</td>
                                <td class="py-2">{{ $project->tasks_count }}</td>
                                <td class="py-2 text-right">
                                    <a class="text-indigo-600 hover:underline"
                                       href="{{ route('coach.projects.show', $project) }}">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-500">Belum ada proyek.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $projects->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>