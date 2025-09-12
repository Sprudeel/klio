<x-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <x-bladewind::card class="p-6">

            {{-- Heading --}}
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-slate-900">Abgabe</h1>
                <x-bladewind::button tag="a"
                                     href="{{ route('assignments.show', $submission->code) }}"
                                     type="secondary"
                                     size="small">
                    Zur Aufgabe
                </x-bladewind::button>
            </div>

            {{-- Success icon & student --}}
            <div class="mt-6 flex items-center gap-4">
                <div class="h-12 w-12 flex items-center justify-center rounded-full bg-indigo-100">
                    <svg class="h-7 w-7 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 20h9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Eingereicht von</p>
                    <p class="text-lg font-medium text-slate-800">{{ $submission->student_name }}</p>
                </div>
            </div>

            {{-- Submission info --}}
            <div class="mt-8 grid sm:grid-cols-2 gap-6">

                <div>
                    <p class="text-sm text-slate-500">Dateiname</p>
                    <p class="font-mono text-slate-800 break-all">
                        {{ $submission->original_filename }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Dateigröße</p>
                    <p class="text-slate-800">{{ number_format($submission->file_size / 1024, 2) }} KB</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Eingereicht am</p>
                    <p class="text-slate-800">
                        {{ $submission->submitted_at->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex items-center gap-4">
                <x-bladewind::button tag="a"
                                     href="{{ route('submissions.download', $submission) }}"
                                     type="primary">
                    PDF herunterladen
                </x-bladewind::button>

                @can('delete', $submission)
                    <form action="{{ route('submissions.destroy', $submission) }}" method="POST"
                          onsubmit="return confirm('Bist du sicher, dass du diese Abgabe löschen möchtest?');">
                        @csrf
                        @method('DELETE')
                        <x-bladewind::button type="error">
                            Löschen
                        </x-bladewind::button>
                    </form>
                @endcan
            </div>
        </x-bladewind::card>
    </div>
</x-layout>
