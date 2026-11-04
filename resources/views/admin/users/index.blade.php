<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Manajemen Pengguna
            </h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                + Tambah Pengguna Baru
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

            @if (session('error'))
                <div class="rounded-md bg-red-100 p-4 text-sm text-red-800 dark:bg-red-900/50 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter & Pencarian -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Tab Filter Role -->
                    <div class="flex flex-wrap gap-2 text-sm">
                        <a href="{{ route('admin.users.index') }}"
                           class="rounded-lg px-3 py-1.5 transition {{ !request('role') && !request('status') ? 'bg-indigo-600 text-white font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}">
                            Semua ({{ $counts['total'] }})
                        </a>
                        <a href="{{ route('admin.users.index', ['role' => 'admin']) }}"
                           class="rounded-lg px-3 py-1.5 transition {{ request('role') === 'admin' ? 'bg-blue-600 text-white font-semibold' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/40 dark:text-blue-300' }}">
                            Admin ({{ $counts['admin'] }})
                        </a>
                        <a href="{{ route('admin.users.index', ['role' => 'coach']) }}"
                           class="rounded-lg px-3 py-1.5 transition {{ request('role') === 'coach' ? 'bg-purple-600 text-white font-semibold' : 'bg-purple-50 text-purple-700 hover:bg-purple-100 dark:bg-purple-950/40 dark:text-purple-300' }}">
                            Coach ({{ $counts['coach'] }})
                        </a>
                        <a href="{{ route('admin.users.index', ['role' => 'mahasiswa']) }}"
                           class="rounded-lg px-3 py-1.5 transition {{ request('role') === 'mahasiswa' ? 'bg-emerald-600 text-white font-semibold' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                            Mahasiswa ({{ $counts['mahasiswa'] }})
                        </a>
                        <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}"
                           class="rounded-lg px-3 py-1.5 transition {{ request('status') === 'inactive' ? 'bg-amber-600 text-white font-semibold' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300' }}">
                            Nonaktif ({{ $counts['inactive'] }})
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
                        <x-text-input name="search" type="text" placeholder="Cari nama, email, NIM..."
                                      value="{{ request('search') }}" class="text-sm w-full md:w-64" />
                        <x-primary-button class="text-xs">Cari</x-primary-button>
                        @if (request('search') || request('role') || request('status'))
                            <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:underline">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar Pengguna -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                <th class="py-3 px-4">Nama</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4">Peran (Role)</th>
                                <th class="py-3 px-4">NIM</th>
                                <th class="py-3 px-4">Status Akun</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="py-3 px-4 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id())
                                            <span class="ml-1 text-xs text-indigo-600 dark:text-indigo-400 font-semibold">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                    <td class="py-3 px-4 capitalize">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                            {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            {{ $user->role === 'coach' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                            {{ $user->role === 'mahasiswa' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : '' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $user->nim ?? '-' }}</td>
                                    <td class="py-3 px-4">
                                        @if ($user->is_active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-200">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-900 dark:text-red-200">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right space-x-2">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="text-indigo-600 hover:underline dark:text-indigo-400 font-medium">
                                            Edit
                                        </a>

                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        onclick="return confirm('Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $user->name }}?')"
                                                        class="text-xs font-semibold {{ $user->is_active ? 'text-amber-600 hover:underline dark:text-amber-400' : 'text-green-600 hover:underline dark:text-green-400' }}">
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                        Tidak ada data pengguna yang sesuai dengan kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
