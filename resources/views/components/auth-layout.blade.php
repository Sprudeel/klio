@php
    $appName = "klio";
@endphp
<html>
<head>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>{{ $appName }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Rye&display=swap" rel="stylesheet">
    <style>

        * {
            font-family: "Inconsolata", monospace;
            font-optical-sizing: auto;
            font-style: normal;
        }
    </style>
    <title>klio</title>
</head>
<body class="bg-white text-slate-900 antialiased">
<header class="border-b border-slate-200">
    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        {{-- Left: Logo + Name --}}
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            {{-- Inline SVG logo (quill + scroll, black on white) --}}
            <img class="w-8" src="{{ Vite::asset('resources/images/klio_logo.svg') }}" alt="Logo">
            <span class="text-2xl font-semibold tracking-tight">{{ $appName }}</span>
        </a>

        {{-- Right: Nav menu --}}
        <div class="flex items-center gap-12">
            <a href="/"
               class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                Abgabe
            </a>
            <a href="/"
               class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                Profil
            </a>
            <a href="/"
               class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                Abmelden
            </a>
        </div>
    </nav>
</header>

<main>
    {{ $slot }}
</main>

@include('partials.footer')
</body>
</html>
