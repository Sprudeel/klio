<x-layout>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    @if($all)
                        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Alle Offene Aufgaben</h1>

                        @else
                            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Offene Aufgaben</h1>
                    @endif
                    <p class="mt-2 text-slate-600">
                        Hallo, <span class="font-medium">{{ auth()->user()->name }}</span>. Hier findest du alle aktuell offenen Aufgaben.
                    </p>
                </div>
                <a href="{{ route('assignments.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-700 px-4 py-2.5 text-white shadow-sm transition hover:bg-blue-800">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Neue Aufgabe
                </a>
            </div>

            {{-- Controls --}}
            <form id="filtersForm" method="GET" action="{{ route('assignments.index') }}" class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="col-span-2 lg:col-span-1 grow">
                    <label class="sr-only" for="q">Suche</label>
                    <div class="relative">
                        <input
                            id="q" name="q" value="{{ request('q') }}"
                            placeholder="Nach Titel, Code… suchen"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-400 pl-10"
                        />
                        <svg class="pointer-events-none absolute right-2 top-2.5 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                <div class="grow">
                    <label class="sr-only" for="due">Fälligkeit</label>
                    <select id="due" name="due" onchange="this.form.requestSubmit()"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Alle Fälligkeiten</option>
                        <option value="today" @selected(request('due')==='today')>Heute fällig</option>
                        <option value="week" @selected(request('due')==='week')>Diese Woche fällig</option>
                        <option value="overdue" @selected(request('due')==='overdue')>Überfällig</option>
                    </select>
                </div>

                <div>
                    <label class="sr-only" for="sort">Sortierung</label>
                    <select id="sort" name="sort" onchange="this.form.requestSubmit()"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="deadline_asc" @selected(request('sort','deadline_asc')==='deadline_asc')>Früheste zuerst</option>
                        <option value="deadline_desc" @selected(request('sort')==='deadline_desc')>Späteste zuerst</option>
                        <option value="created_desc" @selected(request('sort')==='created_desc')>Neueste erstellt</option>
                        <option value="created_asc" @selected(request('sort')==='created_asc')>Älteste erstellt</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-white hover:bg-indigo-700">Filtern</button>
                    <a href="{{ route('assignments.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 hover:bg-slate-50">Zurücksetzen</a>
                </div>
            </form>

            {{-- Summary --}}
            <div class="mt-6 flex flex-wrap items-center gap-3 text-sm text-slate-600">
        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
            {{ $assignments->total() }} gefunden
        </span>
                @if(request('q'))
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1">
                        Suche: “{{ request('q') }}”
                    </span>
                @endif
                @if(request('due'))
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1">
                        Fälligkeit: {{ ['today'=>'Heute','week'=>'Diese Woche','overdue'=>'Überfällig'][request('due')] ?? 'Alle' }}
                    </span>
                @endif
                @if(request('sort'))
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1">
                        Sortierung: {{ ['deadline_asc'=>'Früheste zuerst','deadline_desc'=>'Späteste zuerst','created_desc'=>'Neueste erstellt', 'created_asc' => 'Älteste erstellt'][request('sort')] ?? 'Alle' }}
                    </span>
                @endif
            </div>

            {{-- List/Grid --}}
            @if($assignments->count() > 0)
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($assignments as $a)
                        <a href="{{ route('assignments.show', $a) }}"
                           class="group block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex items-start justify-between">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 text-xs uppercase tracking-wide text-slate-500">
                                        <span>Code</span>
                                        <span class="font-mono text-slate-900">{{ $a->code }}</span>
                                    </div>
                                    <h3 class="mt-2 truncate text-2xl font-semibold text-slate-900 group-hover:text-slate-900/90">
                                        {{ $a->icon }} {{ $a->name }}
                                    </h3>
                                </div>
                                @if($a->color)
                                    <span class="inline-flex h-7 min-w-7 items-center justify-center rounded-full px-2 text-xs"
                                          style="background-color: {{ $a->color }}20; color: {{ $a->color }}">●</span>
                                @endif
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                                @if($a->deadline)
                                    <span class="inline-flex items-center gap-1">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/></svg>
                                Fällig: {{ optional($a->deadline)->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                            </span>
                                    @php
                                        $overdue = $a->deadline && $a->deadline->isPast();
                                    @endphp
                                    @if($overdue)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-red-700">überfällig</span>
                                    @endif
                                @endif

                                <span class="inline-flex items-center gap-1">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z"/><circle cx="12" cy="12" r="3"/></svg>
                            Einreichungen: {{ $a->submissions_count ?? ($a->submissions_count = $a->submissions()->count()) }}
                        </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">offen</span>
                            </div>

                            @if($a->description && !$all)
                                <p class="mt-3 line-clamp-2 text-sm text-slate-600">{{ $a->description }}</p>
                            @else

                                <p class="mt-3 flex gap-3 text-sm text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 22C4 17.5817 7.58172 14 12 14C16.4183 14 20 17.5817 20 22H18C18 18.6863 15.3137 16 12 16C8.68629 16 6 18.6863 6 22H4ZM12 13C8.685 13 6 10.315 6 7C6 3.685 8.685 1 12 1C15.315 1 18 3.685 18 7C18 10.315 15.315 13 12 13ZM12 11C14.21 11 16 9.21 16 7C16 4.79 14.21 3 12 3C9.79 3 8 4.79 8 7C8 9.21 9.79 11 12 11Z"></path></svg>
                                    {{ $a->author->name }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $assignments->withQueryString()->links() }}
                </div>
            @else
                {{-- Empty state --}}
                <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                        <svg class="h-6 w-6 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold">Keine offenen Aufgaben</h3>
                    <p class="mt-1 text-slate-600">Lehn dich zurück – oder erstelle eine neue Aufgabe.</p>
                    <a href="{{ route('assignments.create') }}"
                       class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-white hover:bg-blue-800">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        Aufgabe anlegen
                    </a>
                </div>
            @endif
        </div>
</x-layout>
