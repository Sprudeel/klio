<x-layout>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

            {{-- Header / Title + Actions --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        @if($assignment->color)
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full"
                                  style="background-color: {{ $assignment->color }}20; color: {{ $assignment->color }}">●</span>
                        @endif
                        <h1 class="truncate text-2xl sm:text-3xl font-bold text-slate-900">
                            {{ $assignment->name }}
                        </h1>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/></svg>
                    Fällig: {{ optional($assignment->deadline)->timezone(config('app.timezone'))->format('d.m.Y') ?? '—' }}
                </span>

                        <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z"/><circle cx="12" cy="12" r="3"/></svg>
                    Einreichungen: {{ $assignment->submissions_count ?? $assignment->submissions()->count() }}
                </span>

                        <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4 2 7-6.5-4-6.5 4 2-7L2 9h7z"/></svg>
                    Code: <span class="font-mono text-slate-900">{{ $assignment->code }}</span>
                </span>

                        @if($assignment->is_closed)
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-red-700">geschlossen</span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">offen</span>
                        @endif
                    </div>
                    @if($assignment->description)
                        <p class="mt-3 max-w-2xl text-slate-700">{{ $assignment->description }}</p>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2">
                    {{-- Export buttons --}}
                    <a href="{{ route('assignments.export.zip', $assignment) }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-slate-900 hover:bg-slate-50">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M7 10V4h10v6M7 14h10v6H7zM10 7h4M10 17h4"/>
                        </svg>
                        ZIP herunterladen
                    </a>

                    <a href="{{ route('assignments.export.pdf', $assignment) }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-slate-900 hover:bg-slate-50">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2h7l5 5v15H6z"/><path d="M13 2v6h6"/>
                        </svg>
                        Alle als PDF mergen
                    </a>

                    {{-- Edit / Delete assignment --}}
                    <a href="{{ route('assignments.edit', $assignment) }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-white hover:bg-blue-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                        Aufgabe bearbeiten
                    </a>

                    {{-- Open / Close assignment --}}
                    @if(!$assignment->isClosed)
                        <form class="inline-flex items-center bg-amber-500 rounded-lg" method="POST" action="{{ route('assignments.close', $assignment) }}"
                              onsubmit="return confirm('Diese Aufgabe wirklich schließen? Neue Einreichungen sind dann nicht mehr möglich.')">
                            @csrf
                            <button class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-white hover:bg-amber-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Schließen
                            </button>
                        </form>
                    @else
                        <form class="inline-flex items-center bg-emerald-500 rounded-lg" method="POST" action="{{ route('assignments.open', $assignment) }}"
                              onsubmit="return confirm('Diese Aufgabe wieder öffnen? Schüler:innen können erneut einreichen.')">
                            @csrf
                            <button class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2.5 text-white hover:bg-emerald-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14"/>
                                    <path d="M12 5v14"/>
                                </svg>
                                Öffnen
                            </button>
                        </form>
                    @endif

                    <form class="inline-flex items-center bg-red-700 rounded-lg" method="POST" action="{{ route('assignments.destroy', $assignment) }}"
                          onsubmit="return confirm('Diese Aufgabe wirklich löschen? Dies kann nicht rückgängig gemacht werden.')">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-1 text-white hover:bg-red-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
                            Löschen
                        </button>
                    </form>


                </div>
            </div>

            {{-- Submissions Filter/Search --}}
            <form id="subsFiltersForm" method="GET" action="{{ route('assignments.show', $assignment) }}" class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <input type="hidden" name="keep" value="1">
                <div class="col-span-2 lg:col-span-1">
                    <label class="sr-only" for="q">Suche</label>
                    <div class="relative">
                        <input id="q" name="q" value="{{ request('q') }}" placeholder="Nach Name, Datei…"
                               class="w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-400 pl-10" />
                        <svg class="pointer-events-none absolute right-2 top-2.5 -translate-y-1/2 h-5 w-5 text-slate-400"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="sr-only" for="status">Status</label>
                    <select id="status" name="status" onchange="this.form.requestSubmit()"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Alle</option>
                        <option value="new" @selected(request('status')==='new')>Neu</option>
                        <option value="reviewed" @selected(request('status')==='reviewed')>Gesehen</option>
                        <option value="graded" @selected(request('status')==='graded')>Bewertet</option>
                    </select>
                </div>

                <div>
                    <label class="sr-only" for="sort">Sortierung</label>
                    <select id="sort" name="sort" onchange="this.form.requestSubmit()"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="latest" @selected(request('sort','latest')==='latest')>Neueste zuerst</option>
                        <option value="oldest" @selected(request('sort')==='oldest')>Älteste zuerst</option>
                        <option value="name" @selected(request('sort')==='name')>Name A–Z</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-white hover:bg-indigo-700">Filtern</button>
                    <a href="{{ route('assignments.show', $assignment) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 hover:bg-slate-50">Zurücksetzen</a>
                </div>
            </form>

            {{-- Submissions Table --}}
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <div class="px-4 py-3 sm:px-6 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Einreichungen</h2>
                    <div class="text-sm text-slate-600">{{ $submissions->total() }} Einreichungen</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-y border-slate-200 bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 sm:px-6">Name</th>
                            <th class="px-4 py-3 sm:px-6">Datei</th>
                            <th class="px-4 py-3 sm:px-6">Größe</th>
                            <th class="px-4 py-3 sm:px-6">Status</th>
                            <th class="px-4 py-3 sm:px-6">Eingereicht am</th>
                            <th class="px-4 py-3 sm:px-6 text-right">Aktionen</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @forelse($submissions as $s)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 sm:px-6">
                                    <div class="font-medium text-slate-900">{{ $s->name }}</div>
                                    @if($s->student_identifier)
                                        <div class="text-xs text-slate-500">{{ $s->student_identifier }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-rose-600" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2h7l5 5v15H6z"/><path d="M13 2v6h6"/></svg>
                                        <span class="font-mono truncate max-w-[18ch]" title="{{ $s->filename }}">
                                        {{ \Illuminate\Support\Str::limit($s->filename, 24) }}
                                    </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    @if(isset($s->size_bytes))
                                        {{ number_format($s->size_bytes / 1024, 1, ',', '.') }} KB
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    @php
                                        $status = $s->status ?? 'new';
                                        $badge = [
                                            'new' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'label' => 'Neu'],
                                            'reviewed' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'label' => 'Gesehen'],
                                            'graded' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'label' => 'Bewertet'],
                                        ][$status] ?? ['bg'=>'bg-slate-100','text'=>'text-slate-700','label'=>ucfirst($status)];
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $badge['bg'] }} {{ $badge['text'] }}">
                                    {{ $badge['label'] }}
                                </span>
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    {{ optional($s->created_at)->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <div class="flex justify-end gap-2">
                                        {{-- View (preview PDF) --}}
                                        <a href="{{ route('submissions.show', $s) }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-slate-900 hover:bg-slate-50"
                                           title="Ansehen">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <span class="sr-only sm:not-sr-only">Ansehen</span>
                                        </a>

                                        {{-- Download file --}}
                                        <a href="{{ route('submissions.download', $s) }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-slate-900 hover:bg-slate-50"
                                           title="Download">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M5 21h14"/>
                                            </svg>
                                            <span class="sr-only sm:not-sr-only">Download</span>
                                        </a>

                                        {{-- Edit meta (e.g., status, name) --}}
                                        <a href="{{ route('submissions.edit', $s) }}"
                                           class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700"
                                           title="Bearbeiten">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                            </svg>
                                            <span class="sr-only sm:not-sr-only">Bearbeiten</span>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('submissions.destroy', $s) }}"
                                              onsubmit="return confirm('Diese Einreichung wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 text-white hover:bg-red-700"
                                                    title="Löschen">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M3 6h18"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6M14 11v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                                <span class="sr-only sm:not-sr-only">Löschen</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 sm:px-6 text-center text-slate-600">
                                    Keine Einreichungen vorhanden.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($submissions instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="px-4 py-4 sm:px-6 border-t border-slate-200">
                        {{ $submissions->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
</x-layout>
