<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Edit Data Pengguna: {{ $user->name }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Perbarui profil, peran, atau reset kata sandi akun pengguna
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="title">Perbarui Data Akun</x-slot>
                <x-slot name="subtitle">Perubahan status atau hak akses akan langsung berdampak pada sesi login berikutnya</x-slot>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5"
                      x-data="{ role: '{{ old('role', $user->role) }}' }">
                    @csrf
                    @method('PUT')

                    <!-- Nama -->
                    <div>
                        <x-input-label for="name" value="Nama Lengkap" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      :value="old('name', $user->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Alamat Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full font-mono text-sm"
                                      :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password Baru (Opsional) -->
                    <div>
                        <x-input-label for="password" value="Kata Sandi Baru (Opsional)" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                      placeholder="Kosongkan jika tidak ingin mengubah kata sandi" />
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Isi hanya bila ingin mereset kata sandi akun ini (minimal 8 karakter).</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <!-- Role -->
                    <div>
                        <x-input-label for="role" value="Peran (Role)" />
                        @if ($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div class="mt-1 flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-400">
                                <span class="font-medium capitalize">{{ $user->role }}</span>
                                <span class="italic text-slate-500 dark:text-slate-400">(Tidak dapat mengubah peran akun sendiri)</span>
                            </div>
                        @else
                            <select id="role" name="role" x-model="role"
                                    class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <option value="mahasiswa">Mahasiswa (Peserta Magang)</option>
                                <option value="coach">Coach (Pembimbing)</option>
                                <option value="admin">Admin (Administrator Sistem)</option>
                            </select>
                        @endif
                        <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
                    </div>

                    <!-- NIM (Khusus Mahasiswa) -->
                    <div x-show="role === 'mahasiswa'" x-transition class="p-4 rounded-lg bg-teal-50/50 border border-teal-100 dark:bg-teal-950/20 dark:border-teal-900/50 space-y-1">
                        <x-input-label for="nim" value="Nomor Induk Mahasiswa (NIM)" class="text-teal-900 dark:text-teal-300" />
                        <x-text-input id="nim" name="nim" type="text" class="mt-1 block w-full font-mono text-sm border-teal-300 focus:border-teal-500 focus:ring-teal-500 dark:border-teal-700 dark:bg-slate-900 dark:text-slate-100"
                                      :value="old('nim', $user->nim)" placeholder="Contoh: 2024001" />
                        <p class="text-xs text-teal-700 dark:text-teal-400">Wajib diisi secara unik bila peran akun adalah Mahasiswa.</p>
                        <x-input-error :messages="$errors->get('nim')" class="mt-1" />
                    </div>

                    <!-- Status Aktif -->
                    <div class="pt-2">
                        @if ($user->id === auth()->id())
                            <input type="hidden" name="is_active" value="1">
                            <p class="text-xs text-slate-500 dark:text-slate-400 italic">Akun Anda sendiri selalu aktif dan tidak dapat dinonaktifkan.</p>
                        @else
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:focus:ring-blue-600">
                                <span class="ms-2.5 text-sm font-medium text-slate-700 dark:text-slate-200">Akun Aktif (pengguna dapat masuk ke sistem)</span>
                            </label>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 pt-5 border-t border-slate-200 dark:border-slate-700/80">
                        <x-primary-button>Perbarui Pengguna</x-primary-button>
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center rounded-lg px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>