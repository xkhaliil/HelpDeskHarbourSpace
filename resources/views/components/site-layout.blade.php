<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>
        @if($title)
            {{ $title }} | BLAUG
        @else
            BLAUG
        @endif
    </title>

    @fonts

    <!-- Styles / Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-blue-50 font-sans text-slate-800 antialiased">

<div class="mx-auto flex min-h-screen max-w-6xl flex-col bg-white shadow-sm">
    <x-site-layout-header />

    <main class="flex-1 px-10 py-10">
        {{$slot}}
    </main>

    <x-site-layout-footer/>
</div>

</body>
</html>
