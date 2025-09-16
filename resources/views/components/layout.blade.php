<!DOCTYPE html>
<html lang="de">
@php
    $appName = "klio";
@endphp
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'klio' }}</title>
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
    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 md:flex items-center sm:hidden justify-between  ">
        {{-- Left: Logo + Name --}}
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            {{-- Inline SVG logo (quill + scroll, black on white) --}}
            <img class="w-8" src="/images/klio_logo.svg" alt="Logo">
            <span class="text-2xl font-semibold tracking-tight">{{ $appName }}</span>
        </a>

        @if(!auth()->user())
        <div class="flex items-center gap-12">
            <a href="/submission"
               class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                Abgabe
            </a>
            <a href="/login"
               class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                Login
            </a>
        </div>
        @endif

        @if(auth()->user())
            <div class="flex items-center gap-12">
                @if(auth()->user()->isAdmin())
                    <a href="/assignments/all"
                       class="relative text-blue-500 font-medium transition-colors duration-200 hover:text-blue-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-blue-900
              after:transition-all after:duration-300 hover:after:w-full">
                        Alle Aufgaben
                    </a>
                @endif
                    <a href="/dashboard"
                       class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                        Übersicht
                    </a>
                <a href="/assignments"
                   class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                    Meine Aufgaben
                </a>

                <a href="/profile"
                   class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                    Profil
                </a>
                <a href="/logout"
                   class="relative text-slate-600 font-medium transition-colors duration-200 hover:text-slate-900
              after:absolute after:-bottom-1 after:left-0 after:h-0.5 after:w-0 after:bg-slate-900
              after:transition-all after:duration-300 hover:after:w-full">
                    Abmelden
                </a>
            </div>
        @endif
    </nav>
</header>
{{-- Bottom Mobile Navigation --}}
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 md:hidden z-40">
    <div class="flex justify-around py-2">
        @if(!auth()->user())
            <a href="/submission" class="flex flex-col items-center text-slate-600 hover:text-slate-900">
                {{-- Upload/Abgabe icon --}}
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0l-4 4m4-4l4 4M4 20h16"/>
                </svg>
                <span class="text-xs">Abgabe</span>
            </a>
            <a href="/login" class="flex flex-col items-center text-slate-600 hover:text-slate-900">
                {{-- Login icon --}}
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12l-6-6m0 0v12"/>
                </svg>
                <span class="text-xs">Login</span>
            </a>
        @else
            @if(auth()->user()->isAdmin())
                <a href="/assignments/all" class="flex flex-col items-center text-blue-500 hover:text-blue-900">
                    {{-- Tasks/All icon --}}
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span class="text-xs">Alle Aufgaben</span>
                </a>
            @endif
            <a href="/dashboard" class="flex flex-col items-center text-slate-600 hover:text-slate-900">
                {{-- Home/Übersicht icon --}}
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4.5 10.5V19a2 2 0 002 2h11a2 2 0 002-2v-8.5"/>
                </svg>
                <span class="text-xs">Übersicht</span>
            </a>
            <a href="/assignments" class="flex flex-col items-center text-slate-600 hover:text-slate-900">
                {{-- Aufgaben icon --}}
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect width="16" height="12" x="4" y="6" rx="2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5"/>
                </svg>
                <span class="text-xs">Aufgaben</span>
            </a>
            <a href="/profile" class="flex flex-col items-center text-slate-600 hover:text-slate-900">
                {{-- Profil icon --}}
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 20c0-2.21 3.58-4 8-4s8 1.79 8 4"/>
                </svg>
                <span class="text-xs">Profil</span>
            </a>
            <a href="/logout" class="flex flex-col items-center text-slate-600 hover:text-slate-900">
                {{-- Logout icon --}}
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 15l3-3m0 0l-3-3m3 3H9"/>
                </svg>
                <span class="text-xs">Abmelden</span>
            </a>
        @endif
    </div>
</nav>
<main>
    {{ $slot }}
</main>


@include('partials.footer')
</body>
</html>
