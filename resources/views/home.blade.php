<x-guest-layout>
    <h1 class="text-xl font-bold">Welcome</h1>

    <p class="mt-2 text-sm text-gray-600">
        @auth
            <a href="{{ route('dashboard') }}" class="underline">Go to your dashboard</a>
        @else
            <a href="{{ route('login') }}" class="underline">Log in</a> or <a href="{{ route('register') }}" class="underline">register</a>.
        @endauth
    </p>
</x-guest-layout>
