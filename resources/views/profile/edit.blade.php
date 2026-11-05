<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100">
                    Profil Pengguna
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">
                    Kelola informasi akun pribadi, kredensial masuk, dan preferensi keamanan
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
            <!-- Informasi Profil -->
            <x-card>
                <x-slot name="title">Informasi Akun</x-slot>
                <x-slot name="subtitle">Perbarui nama tampilan dan alamat email yang terdaftar</x-slot>
                @include('profile.partials.update-profile-information-form')
            </x-card>

            <!-- Keamanan Kata Sandi -->
            <x-card>
                <x-slot name="title">Kata Sandi & Keamanan</x-slot>
                <x-slot name="subtitle">Pastikan akun Anda menggunakan kata sandi yang kuat dan aman</x-slot>
                @include('profile.partials.update-password-form')
            </x-card>

            <!-- Kebijakan Akun Terpusat (BR-07) -->
            <div class="rounded-xl border border-blue-200 dark:border-blue-900/60 bg-blue-50/60 dark:bg-blue-950/30 p-5 sm:p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-blue-950 dark:text-blue-200">
                            Pengelolaan Akun Terpusat
                        </h3>
                        <p class="mt-1 text-xs text-blue-900/80 dark:text-blue-300 leading-relaxed">
                            Sesuai kebijakan SIMHOP, registrasi dan pengelolaan status akun pengguna dikelola secara terpusat oleh Administrator. Pengguna tidak dapat menghapus atau menonaktifkan akun sendiri. Apabila Anda memerlukan penonaktifan akun atau mutasi data peran, silakan hubungi Administrator sistem.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>