<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Dashboard Admin
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                    Ringkasan statistik pengguna dan kendali akun operasional SIMHOP
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z" />
                </svg>
                Tambah Pengguna
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <!-- Statistik Pengguna -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <x-stat-card
                    title="Total Pengguna"
                    :value="$stats['total']"
                    color="blue"
                    description="Semua akun terdaftar">
                    <x-slot name="icon">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.765l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card
                    title="Admin Aktif"
                    :value="$stats['admin']"
                    color="purple"
                    description="Hak akses penuh">
                    <x-slot name="icon">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card
                    title="Coach Aktif"
                    :value="$stats['coach']"
                    color="emerald"
                    description="Pembimbing magang">
                    <x-slot name="icon">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card
                    title="Mahasiswa Aktif"
                    :value="$stats['mahasiswa']"
                    color="teal"
                    description="Peserta magang aktif">
                    <x-slot name="icon">
                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card
                    title="Akun Nonaktif"
                    :value="$stats['inactive']"
                    color="rose"
                    description="Akses login ditutup">
                    <x-slot name="icon">
                        <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>

            <!-- Pengguna Terbaru & Tautan Cepat -->
            <x-card>
                <x-slot name="title">Pengguna yang Baru Didaftarkan</x-slot>
                <x-slot name="subtitle">5 akun pengguna terakhir yang dibuat ke dalam sistem</x-slot>
                <x-slot name="actions">
                    <a href="{{ route('admin.users.index') }}"
                       aria-label="Kelola semua akun pengguna"
                       class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        <span>Kelola Semua Pengguna</span>
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </x-slot>

                <div class="overflow-x-auto -mx-6 -my-4">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/80 text-left text-sm">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                            <tr class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                <th scope="col" class="py-3 px-6">Nama</th>
                                <th scope="col" class="py-3 px-6">Email</th>
                                <th scope="col" class="py-3 px-6">Peran</th>
                                <th scope="col" class="py-3 px-6">NIM</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                                <th scope="col" class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($recentUsers as $u)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3.5 px-6 font-medium text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                        {{ $u->name }}
                                        @if ($u->id === auth()->id())
                                            <span class="ml-1.5 text-xs text-blue-600 dark:text-blue-400 font-semibold">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6 font-mono text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ $u->email }}
                                    </td>
                                    <td class="py-3.5 px-6 whitespace-nowrap">
                                        <x-status-badge :status="$u->role" />
                                    </td>
                                    <td class="py-3.5 px-6 font-mono text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ $u->nim ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-6 whitespace-nowrap">
                                        <x-status-badge :status="$u->is_active ? 'active' : 'inactive'" />
                                    </td>
                                    <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.users.edit', $u) }}"
                                           aria-label="Edit akun {{ $u->name }}"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 px-6 text-center text-slate-600 dark:text-slate-400">
                                        Belum ada pengguna terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>