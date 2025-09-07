<x-layout>
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
                    Hallo, {{ auth()->user()->name }} 👋
                </h1>
                <p class="mt-2 text-slate-600">
                    Willkommen zurück bei <span class="font-medium">klio</span>. Hier ist ein schneller Überblick.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('assignments.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Neue Abgabe
                </a>
                <a href="{{ route('assignments.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-slate-900 shadow-sm transition hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h10M4 17h16"/>
                    </svg>
                    Meine Aufgaben
                </a>
            </div>
        </div>

        {{-- Main grid --}}
        <div class="relative mt-16 flex">
            <div class="mx-auto min-w-2/3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Deine neuesten Aufgaben</h2>
                    <a href="{{ route('assignments.index') }}" class="text-sm text-indigo-600 hover:underline">Alle ansehen</a>
                </div>

                @php
                    $latest = isset($assignments) ? $assignments->take(3) : collect();
                @endphp

                @if($latest->isEmpty())
                    <div class="mt-4 rounded-2xl border border-dashed border-slate-300 p-8 text-center">
                        <p class="text-slate-600">Noch keine Aufgaben erstellt.</p>
                        <a href="{{ route('assignments.create') }}"
                           class="mt-3 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-slate-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            Jetzt erste Abgabe anlegen
                        </a>
                    </div>
                @else
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @foreach($latest as $a)
                            <a href="{{ route('assignments.show', $a) }}"
                               class="group block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h2 class="text-2xl font-semibold text-slate-900 group-hover:text-slate-900/90">
                                            {{ $a->name }}
                                        </h2>
                                    </div>
                                    @if($a->color)
                                        <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full px-2 text-xs"
                                              style="background-color: {{ $a->color }}20; color: {{ $a->color }}">●</span>
                                    @endif
                                </div>

                                <div class="text-sm tracking-wide text-slate-500">Code: <span class="font-mono text-slate-900">{{ $a->code }}</span></div>

                                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                                    @if($a->deadline)
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/>
                                            </svg>
                                            Fällig: {{ $a->deadline->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                        </span>
                                    @endif
                                    @if($a->is_closed)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-red-700">geschlossen</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">offen</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif


        @include('partials.updates')

    </section>
</x-layout>
