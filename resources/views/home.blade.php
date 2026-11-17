<x-app-layout :full-width="true">
    <section class="bg-harbour text-white">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
            <h1 class="max-w-3xl text-4xl font-black uppercase tracking-tight sm:text-6xl">Something broken on campus?</h1>

            <p class="mt-6 max-w-2xl text-lg text-white/80">
                Harbour.Space HelpDesk is where students and teachers report IT and campus problems.
            </p>

            <div class="mt-10 flex flex-wrap gap-3">
                @auth
                    <x-button-link :href="route('dashboard')" variant="light">Go to your dashboard</x-button-link>
                @else
                    <x-button-link :href="route('login')" variant="light">Log in</x-button-link>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-white px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Register</a>
                @endauth
            </div>
        </div>
    </section>
</x-app-layout>
