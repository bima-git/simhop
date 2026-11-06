<section>
    <div class="mb-5 flex flex-wrap items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-xs">
        <div class="flex items-center gap-1.5">
            <span class="text-slate-600 dark:text-slate-400">Peran:</span>
            <x-status-badge :status="$user->role" />
        </div>
        @if ($user->nim)
            <span class="text-slate-300 dark:text-slate-600">&bull;</span>
            <div class="flex items-center gap-1.5 font-mono">
                <span class="text-slate-600 dark:text-slate-400">NIM:</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $user->nim }}</span>
            </div>
        @endif
        <span class="text-slate-300 dark:text-slate-600">&bull;</span>
        <div class="flex items-center gap-1.5">
            <span class="text-slate-600 dark:text-slate-400">Status Akun:</span>
            <x-status-badge :status="$user->is_active ? 'active' : 'inactive'" />
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                          :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full font-mono text-sm"
                          :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-xs text-slate-600 dark:text-slate-400">
                    <p>
                        Alamat email Anda belum diverifikasi.
                        <button form="send-verification" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 underline font-medium focus:outline-none">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-emerald-600 dark:text-emerald-400">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-3">
            <x-primary-button>Simpan Profil</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Profil berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>