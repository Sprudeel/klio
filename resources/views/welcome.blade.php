{{-- resources/views/home.blade.php --}}

<html lang="en">
<body class="bg-white text-slate-900 antialiased">

{{-- ====== Top Navigation ====== --}}
<x-layout>

    {{-- ====== Hero ====== --}}
    <section class="relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-20 grid lg:grid-cols-2 gap-8 items-center">
            {{-- Left: Text --}}
            <div>
                <h1 class="text-4xl sm:text-5xl font-bold leading-tight">
                    klio
                </h1>
                <p class="mt-4 text-lg text-slate-600">
                    Die einfache Lösung für Abgaben – ohne Schnickschnack.
                </p>

                <p class="mt-6 text-slate-600">
                    Schüler:innen reichen Aufgaben in Sekunden ein, Lehrkräfte behalten den Überblick.
                    Nur ein Account, ein Code – keine Hürden!
                </p>
            </div>

            {{-- Right: Animated Logo --}}
            <div class="flex justify-center lg:justify-end">
                <x-svg_animation class="w-40 h-auto"/>
            </div>
        </div>
    </section>


    {{-- ====== Bottom Navigation ====== --}}
    <section class="relative bg-slate-50 border-slate-200 py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="text-xs uppercase tracking-wider text-slate-500">Navigation</div>
                <h2 class="mt-1 text-2xl sm:text-3xl font-semibold">Wofür bist du hier?</h2>
            </div>

            <div class="relative grid sm:grid-cols-2 gap-6">
                {{-- Left: Abgaben ansehen --}}
                <a href="/assignments"
                   class="group block rounded-xl border border-slate-200 p-6 hover:border-indigo-300 hover:shadow-sm transition-all bg-white relative">
                    <div class="flex items-start gap-4">
                        <div class="mt-1 h-6 w-6 text-blue-500">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                <path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <div>

                            <div class="font-medium text-slate-900">Abgaben ansehen</div>
                            <p class="mt-1 text-slate-600 text-sm">
                                Übersicht, Filter & schneller Zugriff.
                            </p>
                        </div>
                    </div>
                </a>

                {{-- Right: Abgabe erstellen --}}
                <a href="/submission"
                   class="group block rounded-xl border border-slate-200 p-6 hover:border-indigo-300 hover:shadow-sm transition-all bg-white relative">
                    <div class="flex items-start gap-4">
                        <div class="mt-1 h-6 w-6 text-blue-500">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-slate-900">Abgabe erstellen</div>
                            <p class="mt-1 text-slate-600 text-sm">
                                Datei hochladen, kurze Angaben machen — fertig.
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>


{{-- ====== Features ====== --}}
<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-6">
            <x-bladewind::card class="p-6">
                <div class="flex items-start gap-4">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 7h16M4 12h10M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold">Abgabe mit Code</h3>
                        <p class="mt-1 text-slate-600">Schüler:innen fügen Dateien per Link ein und laden sie hoch – ganz ohne Konto, ganz ohne Hürden.
                            Jede Abgabe erhält einen eindeutigen, teilbaren Code.</p>
                    </div>
                </div>
            </x-bladewind::card>

            <x-bladewind::card class="p-6">
                <div class="flex items-start gap-4">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold">Automatische Organization</h3>
                        <p class="mt-1 text-slate-600"> klio versieht jede Abgabe mit Zeitstempel und ordnet sie automatisch nach Klasse, Aufgabe und Schüler:in – so startet die Korrektur schneller.</p>
                    </div>
                </div>
            </x-bladewind::card>

            <x-bladewind::card class="p-6">
                <div class="flex items-start gap-4">
                    {{-- Standard Icon links --}}
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold flex items-center gap-2">
                            Open Source
                            {{-- GitHub Icon mit Link --}}

                        </h3>
                        <p class="mt-1 text-slate-600">
                            Der Source Code ist öffentlich. Du weißt also, was im Hintergrund passiert. Entwickelt mit &lt;3.
                        </p>

                        <a href="https://github.com/Sprudeel/klio" target="_blank" rel="noopener noreferrer"
                           class="text-slate-600 hover:text-black transition-colors flex gap-4 pt-4">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M12 0C5.37 0 0 5.37 0 12a12 12 0 008.21 11.43c.6.11.82-.26.82-.58v-2.04c-3.34.73-4.04-1.61-4.04-1.61-.55-1.39-1.34-1.76-1.34-1.76-1.09-.74.08-.73.08-.73 1.2.08 1.83 1.23 1.83 1.23 1.07 1.84 2.8 1.31 3.49 1 .11-.77.42-1.31.76-1.61-2.67-.3-5.47-1.34-5.47-5.95 0-1.32.47-2.4 1.23-3.25-.12-.3-.54-1.52.12-3.17 0 0 1.01-.32 3.3 1.23a11.52 11.52 0 016 0c2.3-1.55 3.3-1.23 3.3-1.23.66 1.65.24 2.87.12 3.17.76.85 1.23 1.93 1.23 3.25 0 4.62-2.8 5.65-5.47 5.95.43.37.81 1.1.81 2.22v3.29c0 .32.22.69.82.58A12.01 12.01 0 0024 12c0-6.63-5.37-12-12-12z"/>
                            </svg>
                            Sprudeel - klio
                        </a>
                    </div>
                </div>
            </x-bladewind::card>
        </div>
    </div>
</section>

{{-- ====== CTA Footer ====== --}}
<section class="py-8">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold">Bereit um klio eine Chance zu geben?</h2>
        <p class="mt-3 text-slate-600">Melde dich bei uns und erhalte dein Konto.</p>
    </div>
</section>
</x-layout>
</body>
</html>
