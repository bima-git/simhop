<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard Admin
            </h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                + Tambah Pengguna
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 p-4 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Statistik Pengguna -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Pengguna</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                </div>
                <div class="rounded-lg bg-blue-50 p-6 shadow-sm border border-blue-200 dark:bg-blue-950/30 dark:border-blue-800">
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-400">Admin Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $stats['admin'] }}</p>
                </div>
                <div class="rounded-lg bg-purple-50 p-6 shadow-sm border border-purple-200 dark:bg-purple-950/30 dark:border-purple-800">
                    <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400">Coach Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-purple-900 dark:text-purple-100">{{ $stats['coach'] }}</p>
                </div>
                <div class="rounded-lg bg-emerald-50 p-6 shadow-sm border border-emerald-200 dark:bg-emerald-950/30 dark:border-emerald-800">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Mahasiswa Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-900 dark:text-emerald-100">{{ $stats['mahasiswa'] }}</p>
                </div>
                <div class="rounded-lg bg-amber-50 p-6 shadow-sm border border-amber-200 dark:bg-amber-950/30 dark:border-amber-800">
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">Akun Nonaktif</p>
                    <p class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-100">{{ $stats['inactive'] }}</p>
                </div>
            </div>

            <!-- Pengguna Terbaru & Tautan Cepat -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Pengguna yang Baru Didaftarkan</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                        Kelola Semua Pengguna →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                <th class="py-3 px-4">Nama</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">NIM</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentUsers as $u)
                                <tr>
                                    <td class="py-3 px-4 font-medium">{{ $u->name }}</td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $u->email }}</td>
                                    <td class="py-3 px-4 capitalize">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $u->role === 'admin' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            {{ $u->role === 'coach' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                            {{ $u->role === 'mahasiswa' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : '' }}">
                                            {{ $u->role }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $u->nim ?? '-' }}</td>
                                    <td class="py-3 px-4">
                                        @if ($u->is_active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('admin.users.edit', $u) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">Belum ada pengguna terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
