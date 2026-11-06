<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Proyek Saya
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                    Daftar seluruh proyek bimbingan magang yang Anda kelola
                </p>
            </div>
            <a href="{{ route('coach.projects.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                + Buat Proyek Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <x-card>
                <div class="overflow-x-auto -mx-6 -my-4">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/80 text-left text-sm">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                            <tr class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                <th scope="col" class="py-3 px-6">Nama Proyek</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                                <th scope="col" class="py-3 px-6">Anggota</th>
                                <th scope="col" class="py-3 px-6">Tugas</th>
                                <th scope="col" class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($projects as $project)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                            <a href="{{ route('coach.projects.show', $project) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                                                {{ $project->name }}
                                            </a>
                                        </div>
                                        @if ($project->description)
                                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 line-clamp-1 max-w-md">
                                                {{ $project->description }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <x-status-badge :status="$project->status" />
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300">
                                        <span class="inline-flex items-center gap-1 font-medium">
                                            <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.765l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                            {{ $project->members_count }} Mahasiswa
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300">
                                        <span class="inline-flex items-center gap-1 font-medium">
                                            <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                            </svg>
                                            {{ $project->tasks_count }} Tugas
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <a href="{{ route('coach.projects.show', $project) }}"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 px-6 text-center text-slate-600 dark:text-slate-400">
                                        Belum ada proyek.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($projects->hasPages())
                    <x-slot name="footer">
                        {{ $projects->links() }}
                    </x-slot>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>