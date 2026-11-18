<x-app-layout title="Register">
    <x-card class="mx-auto max-w-md sm:p-8">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-breeze.input-label for="name" value="Name" />
                <x-breeze.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-breeze.input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-breeze.input-label for="email" value="Email" />
                <x-breeze.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@{{ config('helpdesk.email_domain') }}" />
                <p class="mt-1 text-xs text-gray-500">Use your university email address (ending in &#64;{{ config('helpdesk.email_domain') }}).</p>
                <x-breeze.input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-breeze.input-label for="password" value="Password" />

                <x-breeze.text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-breeze.input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-breeze.input-label for="password_confirmation" value="Confirm Password" />

                <x-breeze.text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <x-breeze.input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-harbour hover:text-harbour-dark rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-harbour" href="{{ route('login') }}">
                    Already registered?
                </a>

                <x-breeze.primary-button class="ms-4">
                    Register
                </x-breeze.primary-button>
            </div>
        </form>
    </x-card>
</x-app-layout>
