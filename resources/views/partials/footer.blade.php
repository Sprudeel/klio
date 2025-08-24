<footer class="mt-16 border-t border-slate-200 bg-white/60 backdrop-blur">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
        {{-- Left: repo + version/commit --}}
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
            @if (!empty($build['source_url']))
                <a href="{{ $build['source_url'] }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 hover:text-slate-900 transition-colors">
                    {{-- GitHub mark --}}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M12 .3A12 12 0 0 0 0 12.5a12 12 0 0 0 8.2 11.4c.6.1.8-.2.8-.6v-2.1c-3.3.7-4-1.6-4-1.6-.6-1.4-1.3-1.8-1.3-1.8-1.1-.7.1-.8.1-.8 1.2.1 1.8 1.2 1.8 1.2 1.1 1.9 2.8 1.3 3.5 1 .1-.8.4-1.3.8-1.6-2.7-.3-5.5-1.3-5.5-6 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.2 0 0 1-.3 3.3 1.2a11.6 11.6 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.6 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.7-2.8 5.7-5.5 6 .4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6A12 12 0 0 0 24 12.5 12 12 0 0 0 12 .3Z"/>
                    </svg>
                    <span class="underline underline-offset-2">Source Code</span>
                </a>
                <span class="hidden sm:inline text-slate-300">|</span>
                <span class="text-slate-300">•</span>
            @endif

            <span>Version <span class="font-medium text-slate-800">{{ $build['version'] }}</span></span>

            @if (!empty($build['commit']))
                <span class="text-slate-300">•</span>
                @php
                    $commitUrl = $build['source_url']
                        ? rtrim($build['source_url'], '/').'/commit/'.$build['commit']
                        : null;
                @endphp
                @if ($commitUrl)
                    <a href="{{ $commitUrl }}" target="_blank" rel="noopener noreferrer"
                       class="font-mono text-xs text-slate-700 hover:text-slate-900 transition-colors"
                       title="Git Commit {{ $build['commit'] }}">
                        {{ $build['commit'] }}
                    </a>
                @else
                    <span class="font-mono text-xs">{{ $build['commit'] }}</span>
                @endif
            @endif
        </div>

        {{-- Right: author / year / env --}}
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
            <span class="text-slate-500">© {{ now()->year }} klio</span>
            <span class="text-slate-300">•</span>
            <span>Made by
                @if(!empty($build['author']['github']))
                    <a href="{{ $build['author']['github'] }}" target="_blank" rel="noopener noreferrer"
                       class="font-medium text-slate-800 hover:text-slate-900 underline underline-offset-2">
                        {{ $build['author']['name'] }}
                    </a>
                @else
                    <span class="font-medium text-slate-800">{{ $build['author']['name'] }}</span>
                @endif
            </span>
            @if (app()->environment() !== 'production')
                <span class="text-slate-300">•</span>
                <span class="uppercase tracking-wide text-xs text-slate-500">{{ $build['env'] }}</span>
            @endif
        </div>
    </div>
</footer>
