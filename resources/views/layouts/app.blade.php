<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' | ' : '' }}{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet"/>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- No Vite build yet: load Tailwind CSS and Alpine.js from a CDN, with the same theme file -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style type="text/tailwindcss">{!! file_get_contents(resource_path('css/theme.css')) !!}</style>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
    @endif
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-ink antialiased">
    {{-- The menu comes from App\View\Components\AppLayout, so it is the same in the header and the footer --}}
    <x-header :menu="$menu" />

    <!-- Page heading -->
    @if ($title)
        <header class="border-b border-gray-200 bg-gray-50">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-8 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-black uppercase tracking-tight sm:text-4xl">{{ $title }}</h1>

                @isset($actions)
                    <div class="flex flex-wrap items-center gap-3">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </header>
    @endif

    <main class="flex-1">
        <!-- Flash messages -->
        @if (session('success') || session('error'))
            <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div role="status" class="rounded-md border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div role="alert" class="rounded-md border-l-4 border-red-600 bg-red-50 px-4 py-3 text-sm font-medium text-red-900">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Page content -->
        @if ($fullWidth)
            {{ $slot }}
        @else
            <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        @endif
    </main>

    <x-footer :menu="$menu" />
</body>
</html>
