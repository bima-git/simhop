<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-700 dark:text-slate-300 font-medium" />
            <x-text-input id="email" class="block mt-1 w-full rounded-lg border-slate-300 dark:border-slate-700 focus:border-blue-500 focus:ring-blue-500 shadow-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" class="text-slate-700 dark:text-slate-300 font-medium" />
            <x-text-input id="password" class="block mt-1 w-full rounded-lg border-slate-300 dark:border-slate-700 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700" name="remember">
                <span class="ms-2 text-xs text-slate-600 dark:text-slate-400 font-medium">{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Masuk ke SIMHOP') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
