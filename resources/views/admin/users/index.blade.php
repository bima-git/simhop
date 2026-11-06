<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Manajemen Pengguna
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                    Kelola seluruh data pengguna, penetapan peran, dan status akun sistem
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z" />
                </svg>
                Tambah Pengguna Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <!-- Filter & Pencarian -->
            <x-card>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Tab Filter Role -->
                    <div class="flex flex-wrap items-center gap-1.5 text-xs">
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-medium transition {{ !request('role') && !request('status') ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                            Semua
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ !request('role') && !request('status') ? 'bg-blue-700 text-white' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">{{ $counts['total'] }}</span>
                        </a>
                        <a href="{{ route('admin.users.index', ['role' => 'admin']) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-medium transition {{ request('role') === 'admin' ? 'bg-purple-600 hover:bg-purple-700 text-white shadow-sm' : 'bg-purple-50 text-purple-700 hover:bg-purple-100 dark:bg-purple-950/40 dark:text-purple-300 dark:hover:bg-purple-900/60' }}">
                            Admin
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ request('role') === 'admin' ? 'bg-purple-700 text-white' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' }}">{{ $counts['admin'] }}</span>
                        </a>
                        <a href="{{ route('admin.users.index', ['role' => 'coach']) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-medium transition {{ request('role') === 'coach' ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/40 dark:text-blue-300 dark:hover:bg-blue-900/60' }}">
                            Coach
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ request('role') === 'coach' ? 'bg-blue-700 text-white' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">{{ $counts['coach'] }}</span>
                        </a>
                        <a href="{{ route('admin.users.index', ['role' => 'mahasiswa']) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-medium transition {{ request('role') === 'mahasiswa' ? 'bg-teal-700 hover:bg-teal-800 text-white shadow-sm' : 'bg-teal-50 text-teal-700 hover:bg-teal-100 dark:bg-teal-950/40 dark:text-teal-300 dark:hover:bg-teal-900/60' }}">
                            Mahasiswa
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ request('role') === 'mahasiswa' ? 'bg-teal-800 text-white' : 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200' }}">{{ $counts['mahasiswa'] }}</span>
                        </a>
                        <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-medium transition {{ request('status') === 'inactive' ? 'bg-rose-700 hover:bg-rose-800 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/60' }}">
                            Nonaktif
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ request('status') === 'inactive' ? 'bg-rose-800 text-white' : 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200' }}">{{ $counts['inactive'] }}</span>
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                        @if (request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
                        @endif
                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="relative w-full md:w-64">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-4 h-4 text-slate-400" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <x-text-input name="search" type="text" placeholder="Cari nama, email, NIM..."
                                          value="{{ request('search') }}" class="pl-9 text-xs w-full py-1.5" />
                        </div>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                            Cari
                        </button>
                        @if (request('search') || request('role') || request('status'))
                            <a href="{{ route('admin.users.index') }}" class="text-xs text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:underline">Reset</a>
                        @endif
                    </form>
                </div>
            </x-card>

            <!-- Tabel Daftar Pengguna -->
            <x-card>
                <div class="overflow-x-auto -mx-6 -my-4">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/80 text-left text-sm">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                            <tr class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                <th scope="col" class="py-3 px-6">Nama</th>
                                <th scope="col" class="py-3 px-6">Email</th>
                                <th scope="col" class="py-3 px-6">Peran (Role)</th>
                                <th scope="col" class="py-3 px-6">NIM</th>
                                <th scope="col" class="py-3 px-6">Status Akun</th>
                                <th scope="col" class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($users as $user)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3.5 px-6 font-medium text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id())
                                            <span class="ml-1.5 text-xs text-blue-600 dark:text-blue-400 font-semibold">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6 font-mono text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ $user->email }}
                                    </td>
                                    <td class="py-3.5 px-6 whitespace-nowrap">
                                        <x-status-badge :status="$user->role" />
                                    </td>
                                    <td class="py-3.5 px-6 font-mono text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ $user->nim ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-6 whitespace-nowrap">
                                        <x-status-badge :status="$user->is_active ? 'active' : 'inactive'" />
                                    </td>
                                    <td class="py-3.5 px-6 text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           aria-label="Edit akun {{ $user->name }}"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            Edit
                                        </a>

                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        onclick="return confirm('Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $user->name }}?')"
                                                        class="inline-flex items-center rounded px-2 py-0.5 text-xs font-semibold transition border {{ $user->is_active ? 'text-amber-700 bg-amber-50 border-amber-200 hover:bg-amber-100 dark:text-amber-300 dark:bg-amber-950/50 dark:border-amber-800' : 'text-emerald-700 bg-emerald-50 border-emerald-200 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/50 dark:border-emerald-800' }}">
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 px-6 text-center text-slate-600 dark:text-slate-400">
                                        Tidak ada data pengguna yang sesuai dengan kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <x-slot name="footer">
                        {{ $users->links() }}
                    </x-slot>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>