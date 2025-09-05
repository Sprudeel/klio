<x-auth-layout>
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
                    Meine Abgaben
                </a>
            </div>
        </div>

        {{-- Main grid --}}
        <div class="relative mt-16 flex">
            <div class="mx-auto min-w-2/3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Deine neuesten Abgaben</h2>
                    <a href="{{ route('assignments.index') }}" class="text-sm text-indigo-600 hover:underline">Alle ansehen</a>
                </div>

                @php
                    $latest = isset($assignments) ? $assignments->take(3) : collect();
                @endphp

                @if($latest->isEmpty())
                    <div class="mt-4 rounded-2xl border border-dashed border-slate-300 p-8 text-center">
                        <p class="text-slate-600">Noch keine Abgaben erstellt.</p>
                        <a href="{{ route('assignments.create') }}"
                           class="mt-3 inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-white hover:bg-slate-800">
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
                                        <div class="text-sm uppercase tracking-wide text-slate-500">Code</div>
                                        <div class="mt-0.5 font-mono text-slate-900">{{ $a->code }}</div>
                                    </div>
                                    @if($a->color)
                                        <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full px-2 text-xs"
                                              style="background-color: {{ $a->color }}20; color: {{ $a->color }}">●</span>
                                    @endif
                                </div>
                                <h3 class="mt-3 text-lg font-semibold text-slate-900 group-hover:text-slate-900/90">
                                    {{ $a->name }}
                                </h3>
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

                @php
                    if (!isset($updates)) {
                        $updates = [
                            [
                                'title' => 'Sammel‑PDF für Abgaben',
                                'desc'  => 'Alle PDFs einer Aufgabe zu einem Dokument zusammenführen – ideal fürs schnelle Korrigieren.',
                                'tag'   => 'Neu',
                                'date'  => now()->format('d.m.Y'),
                            ],
                            [
                                'title' => 'Private Speicherung',
                                'desc'  => 'Uploads liegen auf dem privaten Storage. Downloads laufen über Policies und sind nur für Berechtigte sichtbar.',
                                'tag'   => 'Sicher',
                                'date'  => null,
                            ],
                            [
                                'title' => 'Fußzeile mit Version & Commit',
                                'desc'  => 'Footer zeigt die App‑Version und den 8‑stelligen Commit‑Hash der laufenden Deployment‑Version.',
                                'tag'   => 'Info',
                                'date'  => null,
                            ],
                        ];
                    }
                @endphp

                <div class="mt-10">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        Was ist neu?
                        @if(!empty($build['version']))
                            <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">v{{ $build['version'] }}</span>
                        @endif
                    </h2>

                    <div class="mt-4 space-y-3">
                        @foreach($updates as $u)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-medium text-slate-900">{{ $u['title'] }}</h3>
                                            @if(!empty($u['tag']))
                                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $u['tag'] }}</span>
                                            @endif
                                        </div>
                                        @if(!empty($u['desc']))
                                            <p class="mt-1 text-sm text-slate-600">{{ $u['desc'] }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center gap-3 text-xs text-slate-500">
                                            @if(!empty($u['date']))
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/></svg>
                                                    {{ $u['date'] }}
                                                </span>
                                            @endif
                                            @if(!empty($build['commit']))
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18m9-9H3"/></svg>
                                                    Commit: <span class="font-mono">{{ $build['commit'] }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-auth-layout>
