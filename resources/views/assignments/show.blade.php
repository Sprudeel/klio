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
                            {{ $assignment->icon }} {{ $assignment->name }}
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

                        @if($assignment->isClosed)
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-red-700">geschlossen</span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-emerald-700">offen</span>
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
                            <th class="px-4 py-3 sm:px-6">Eingereicht am</th>
                            <th class="px-4 py-3 sm:px-6 text-right">Aktionen</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @forelse($submissions as $s)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 sm:px-6">
                                    <div class="font-medium text-slate-900">{{ $s->name }}</div>
                                    @if($s->student_name)
                                        <div class="text-xs text-slate-500">{{ $s->student_name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M21 8V20.9932C21 21.5501 20.5552 22 20.0066 22H3.9934C3.44495 22 3 21.556 3 21.0082V2.9918C3 2.45531 3.4487 2 4.00221 2H14.9968L21 8ZM19 9H14V4H5V20H19V9ZM8 7H11V9H8V7ZM8 11H16V13H8V11ZM8 15H16V17H8V15Z"></path>
                                        </svg>
                                        <span class="font-mono truncate max-w-[18ch]" title="{{ $s->original_filename }}">
                                        {{ \Illuminate\Support\Str::limit($s->original_filename, 24) }}
                                    </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 sm:px-6">
                                    @if(isset($s->file_size))
                                        {{ number_format($s->file_size / 1024, 1, ',', '.') }} KB
                                    @else
                                        —
                                    @endif
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
                                        <a href="{{ route('submissions.edit', [$assignment, $s]) }}"
                                           class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700"
                                           title="Bearbeiten">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                            </svg>
                                            <span class="sr-only sm:not-sr-only">Bearbeiten</span>
                                        </a>

                                        {{-- Delete --}}
                                        @can('delete', $s)
                                            <form method="POST" class="inline-flex rounded-lg bg-red-600 text-white hover:bg-red-700" action="{{ route('submissions.destroy',  $s) }}" onsubmit="return confirm('Diese Einreichung wirklich löschen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 text-white" title="Löschen">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 6h18"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                        <path d="M10 11v6M14 11v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                    <span class="sr-only sm:not-sr-only">Löschen</span>
                                                </button>
                                            </form>
                                        @endcan
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
