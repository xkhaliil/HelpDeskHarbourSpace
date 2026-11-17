<footer class="bg-ink text-white">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:grid-cols-3 sm:px-6 lg:px-8">
        <div class="sm:col-span-2">
            <x-wordmark class="text-xl" />

            <p class="mt-3 max-w-md text-sm text-white/70">
                IT and campus support for the students and teachers of Harbour.Space University in Barcelona.
                Report a problem and follow it until it is solved.
            </p>
        </div>

        <div>
            <h2 class="text-xs font-bold uppercase tracking-widest text-white/50">HelpDesk</h2>

            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a></li>
                    <li><a href="{{ route('profile.edit') }}" class="hover:underline">Profile</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:underline">Log in</a></li>
                    <li><a href="{{ route('register') }}" class="hover:underline">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <p class="mx-auto max-w-6xl px-4 py-4 text-xs text-white/50 sm:px-6 lg:px-8">
            &copy; {{ date('Y') }} Harbour.Space HelpDesk &middot; A student project, not an official Harbour.Space University service.
        </p>
    </div>
</footer>
