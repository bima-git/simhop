<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-l-4 border-amber-500">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Pengelolaan Akun
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Sesuai kebijakan SIMHOP, akun pengguna dikelola secara terpusat oleh Administrator. Pengguna tidak dapat menghapus atau menonaktifkan akun sendiri. Apabila memerlukan penonaktifan akun, silakan hubungi Administrator.
                        </p>
                    </header>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
