<x-app-layout title="Log in">
    <x-card class="mx-auto max-w-md sm:p-8">
        <!-- Session Status -->
        <x-breeze.auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @production
            @else
                <div class="mb-6 rounded-md bg-harbour-light p-3 text-sm text-harbour">
                    <p class="font-bold">Demo accounts (password: password)</p>
                    <ul class="mt-1">
                        <li>admin@example.com</li>
                        <li>teacher@harbour.space</li>
                        <li>student@harbour.space</li>
                    </ul>
                </div>
            @endproduction

            <!-- Email Address -->
            <div>
                <x-breeze.input-label for="email" value="Email" />
                <x-breeze.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-breeze.input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-breeze.input-label for="password" value="Password" />

                <x-breeze.text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                <x-breeze.input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded-sm border-gray-300 text-harbour shadow-xs focus:ring-harbour" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-harbour hover:text-harbour-dark rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-harbour" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                @endif

                <x-breeze.primary-button class="ms-3">
                    Log in
                </x-breeze.primary-button>
            </div>
        </form>
    </x-card>
</x-app-layout>
