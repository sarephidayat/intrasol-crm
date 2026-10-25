<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 flex flex-col items-center text-center gap-2">
        <div class="flex h-14 w-14 items-center justify-center border-2 border-[#171717] bg-white shadow-[3px_3px_0_#171717] overflow-hidden">
            <img src="{{ asset('images/logo.webp') }}" alt="Logo PT Integrasi Solusi Teknologi" class="h-12 w-12 object-contain">
        </div>
        <div>
            <div class="font-['Space_Grotesk'] text-2xl font-semibold leading-tight">INTRASOL</div>
            <div class="text-sm text-[#171717]/80">Cash Management</div>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="border-2 border-[#171717] rounded-none text-[#171717] shadow-none focus:ring-[#F5E64D]" name="remember">
                <span class="ms-2 text-sm text-[#171717]">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6 gap-2">
            @if (Route::has('password.request'))
                <a class="text-sm underline decoration-2 underline-offset-2 hover:bg-[#F5E64D]/60" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @else
                <span></span>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
