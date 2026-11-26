{{-- The public welcome page: what the HelpDesk is, how to report a problem and the categories. --}}
<x-app-layout :full-width="true">
    <!-- Hero -->
    <section class="bg-harbour text-white">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 sm:py-28 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-white/70">Harbour.Space University &middot; Barcelona</p>

            <h1 class="mt-4 max-w-3xl text-4xl font-black uppercase leading-none tracking-tight sm:text-6xl">Something broken on campus?</h1>

            <p class="mt-6 max-w-2xl text-lg text-white/80">
                Harbour.Space HelpDesk is where students and teachers report IT and campus problems,
                like a projector that does not turn on or Wi-Fi that keeps disconnecting.
                Every problem becomes a ticket that the IT and campus team handles until it is solved.
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

    <!-- How it works -->
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-black uppercase tracking-tight sm:text-4xl">How to report a problem</h2>

            <ol class="mt-10 grid gap-6 md:grid-cols-3">
                <li class="rounded-xl border border-gray-200 p-6">
                    <span class="text-5xl font-black text-harbour">01</span>
                    <h3 class="mt-4 text-lg font-black uppercase tracking-tight">Log in</h3>
                    <p class="mt-2 text-gray-600">
                        Log in, or create an account. With your &#64;{{ config('helpdesk.student_email_domain') }}
                        or &#64;{{ config('helpdesk.teacher_email_domain') }} address you can start right away.
                        Other addresses are approved by an administrator first.
                    </p>
                </li>
                <li class="rounded-xl border border-gray-200 p-6">
                    <span class="text-5xl font-black text-harbour">02</span>
                    <h3 class="mt-4 text-lg font-black uppercase tracking-tight">Create a ticket</h3>
                    <p class="mt-2 text-gray-600">
                        Describe the problem, choose a category and tell us where it happens.
                        @if (config('helpdesk.screenshots_enabled'))
                            You can add a screenshot.
                        @endif
                        Teachers can also set the priority.
                    </p>
                </li>
                <li class="rounded-xl border border-gray-200 p-6">
                    <span class="text-5xl font-black text-harbour">03</span>
                    <h3 class="mt-4 text-lg font-black uppercase tracking-tight">Follow it</h3>
                    <p class="mt-2 text-gray-600">
                        Follow the status of your ticket, talk with the IT and campus team,
                        and close the ticket when the problem is solved.
                    </p>
                </li>
            </ol>

            <div class="mt-10 flex flex-wrap items-center gap-3 rounded-xl bg-gray-50 p-6">
                <span class="text-sm font-bold uppercase tracking-widest text-gray-500">A ticket goes from</span>
                @foreach ($statuses as $status)
                    <x-badge :value="$status" />
                    @if (! $loop->last)
                        <span aria-hidden="true" class="text-gray-400">&rarr;</span>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- What can be reported -->
    @if ($categories->isNotEmpty())
        <section class="border-t border-gray-200 bg-gray-50">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-black uppercase tracking-tight sm:text-4xl">What can you report?</h2>

                <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('categories.show', $category) }}" class="block h-full rounded-xl border-l-4 border-harbour bg-white p-5 shadow-sm transition hover:shadow-md">
                                <span class="font-bold">{{ $category->name }}</span>
                                <span class="mt-1 block text-sm text-gray-600">{{ $category->description }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <!-- Call to action -->
    <section class="border-b border-white/10 bg-ink text-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-6 px-4 py-16 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-black uppercase tracking-tight">Ready to report a problem?</h2>

            @auth
                <x-button-link :href="route('tickets.create')" variant="light">New ticket</x-button-link>
            @else
                <x-button-link :href="route('register')" variant="light">Create an account</x-button-link>
            @endauth
        </div>
    </section>
</x-app-layout>
