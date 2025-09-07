<div class="mt-10">
    <h2 class="text-xl font-semibold flex items-center gap-2">
        Was ist neu?
        @if(!empty($build['version']))
            <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">v{{ $build['version'] }}</span>
        @endif
    </h2>

    @php
        // Map icon + color by type
        $typeClasses = [
            'feature'  => ['bg' => 'bg-indigo-50',  'fg' => 'text-indigo-600'],
            'fix'      => ['bg' => 'bg-emerald-50', 'fg' => 'text-emerald-600'],
            'security' => ['bg' => 'bg-red-50',     'fg' => 'text-red-600'],
            'perf'     => ['bg' => 'bg-amber-50',   'fg' => 'text-amber-600'],
            'docs'     => ['bg' => 'bg-sky-50',     'fg' => 'text-sky-600'],
            'misc'     => ['bg' => 'bg-slate-100',  'fg' => 'text-slate-600'],
        ];

        $icon = function (string $type) {
            switch ($type) {
                case 'feature':  // star
                    return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4 2 7-6.5-4-6.5 4 2-7L2 9h7z"/></svg>';
                case 'fix':      // check in diamond
                    return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l9 9-9 9-9-9 9-9z"/><path d="M8.5 12l2.5 2.5L15.5 10"/></svg>';
                case 'security': // shield + check
                    return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6a10 10 0 1 1-16 0V6l8-4z"/><path d="M9 12l2 2 4-4"/></svg>';
                case 'perf':     // clock/needle
                    return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 18 0A9 9 0 1 0 3 12"/><path d="M12 7v5l3 3"/></svg>';
                case 'docs':     // file
                    return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h7l5 5v15H6z"/><path d="M13 2v6h6"/></svg>';
                default:         // dot
                    return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>';
            }
        };
    @endphp

    <div class="mt-4 space-y-3">
        @forelse(($updates ?? []) as $u)
            @php
                $type   = strtolower($u['type'] ?? 'misc');
                $colors = $typeClasses[$type] ?? $typeClasses['misc'];
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-xl {{ $colors['bg'] }} {{ $colors['fg'] }}">
                        {!! $icon($type) !!}
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
                                    {{ \Illuminate\Support\Carbon::parse($u['date'])->timezone(config('app.timezone'))->format('d.m.Y') }}
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
        @empty
            <div class="mt-4 rounded-2xl border border-dashed border-slate-300 p-6 text-slate-600">
                Noch keine Neuigkeiten eingetragen.
            </div>
        @endforelse
    </div>
</div>
