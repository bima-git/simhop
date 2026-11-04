<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Edit Data Pengguna: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:text-gray-100">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4"
                      x-data="{ role: '{{ old('role', $user->role) }}' }">
                    @csrf
                    @method('PUT')

                    <!-- Nama -->
                    <div>
                        <x-input-label for="name" value="Nama Lengkap" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      :value="old('name', $user->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Alamat Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                      :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password Baru (Opsional) -->
                    <div>
                        <x-input-label for="password" value="Kata Sandi Baru (Opsional)" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                      placeholder="Kosongkan jika tidak ingin mengubah kata sandi" />
                        <p class="mt-1 text-xs text-gray-500">Isi hanya jika ingin mereset kata sandi pengguna ini (minimal 8 karakter).</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <x-input-label for="role" value="Peran (Role)" />
                        @if ($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <p class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 p-2 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-400 capitalize">
                                {{ $user->role }} (Tidak dapat mengubah peran akun sendiri)
                            </p>
                        @else
                            <select id="role" name="role" x-model="role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="mahasiswa">Mahasiswa (Peserta Magang)</option>
                                <option value="coach">Coach (Pembimbing)</option>
                                <option value="admin">Admin (Administrator Sistem)</option>
                            </select>
                        @endif
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- NIM (Khusus Mahasiswa) -->
                    <div x-show="role === 'mahasiswa'" x-transition>
                        <x-input-label for="nim" value="Nomor Induk Mahasiswa (NIM)" />
                        <x-text-input id="nim" name="nim" type="text" class="mt-1 block w-full"
                                      :value="old('nim', $user->nim)" placeholder="Contoh: 2024001" />
                        <p class="mt-1 text-xs text-gray-500">Wajib diisi bila peran pengguna adalah Mahasiswa.</p>
                        <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                    </div>

                    <!-- Status Aktif -->
                    <div class="pt-2">
                        @if ($user->id === auth()->id())
                            <input type="hidden" name="is_active" value="1">
                            <p class="text-xs text-gray-500">Akun Anda sendiri selalu aktif dan tidak dapat dinonaktifkan.</p>
                        @else
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600">
                                <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">Akun Aktif (pengguna dapat masuk ke sistem)</span>
                            </label>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-primary-button>Perbarui Pengguna</x-primary-button>
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline dark:text-gray-400">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
