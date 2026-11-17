<nav x-data="{ open: false }" class="bg-harbour text-white">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex items-center gap-10">
                <!-- Wordmark -->
                <a href="{{ route('home') }}" class="rounded-sm text-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">
                    <x-wordmark />
                </a>

                <!-- Navigation Links -->
                @auth
                    <div class="hidden h-full gap-6 sm:flex">
                        <x-breeze.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            Dashboard
                        </x-breeze.nav-link>
                    </div>
                @endauth
            </div>

            <!-- Settings Dropdown / Guest Links -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                @auth
                    <x-breeze.dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold text-white transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-white">
                                <span>{{ Auth::user()->name }}</span>
                                <span class="rounded-full bg-white/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest">{{ Auth::user()->role->label() }}</span>

                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-breeze.dropdown-link :href="route('profile.edit')">
                                Profile
                            </x-breeze.dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-breeze.dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    Log Out
                                </x-breeze.dropdown-link>
                            </form>
                        </x-slot>
                    </x-breeze.dropdown>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-white">Log in</a>
                    <x-button-link :href="route('register')" variant="light">Register</x-button-link>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-white transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-white" aria-label="Open the menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-white/10 sm:hidden">
        @auth
            <div class="space-y-1 pb-3 pt-2">
                <x-breeze.responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-breeze.responsive-nav-link>
            </div>

            <!-- Responsive Settings Options -->
            <div class="border-t border-white/10 pb-1 pt-4">
                <div class="px-4">
                    <div class="text-base font-semibold">{{ Auth::user()->name }} &middot; {{ Auth::user()->role->label() }}</div>
                    <div class="text-sm text-white/70">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-breeze.responsive-nav-link :href="route('profile.edit')">
                        Profile
                    </x-breeze.responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-breeze.responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            Log Out
                        </x-breeze.responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="space-y-1 pb-3 pt-2">
                <x-breeze.responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                    Log in
                </x-breeze.responsive-nav-link>
                <x-breeze.responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                    Register
                </x-breeze.responsive-nav-link>
            </div>
        @endauth
    </div>
</nav>
