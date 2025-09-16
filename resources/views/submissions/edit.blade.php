<x-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if($errors->any())
            <x-bladewind::card type="error" show_close_icon="true" class="mb-6">
                <div class="font-semibold">Bitte korrigiere die folgenden Fehler:</div>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            </x-bladewind::card>
        @endif

        <x-bladewind::card class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Abgabe bearbeiten</h1>
                    <p class="mt-1 text-slate-600">
                        Aufgabe: <span class="font-medium">{{ $assignment->name }}</span>
                    </p>
                </div>

                <x-bladewind::button tag="a" href="{{ route('assignments.show', $assignment) }}" type="secondary" size="small">
                    Zurück zur Aufgabe
                </x-bladewind::button>
            </div>

            <form class="mt-8 space-y-6"
                  method="POST"
                  action="{{ route('submissions.update', [$assignment, $submission]) }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="grid sm:grid-cols-2 gap-6">
                    <x-bladewind::input
                        name="student_name"
                        label="Name / Kürzel"
                        required="true"
                        show_inline_error="true"
                        value="{{ old('student_name', $submission->student_name) }}"
                    />
                </div>

                {{-- Current file preview/info --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm text-slate-600">Aktuelle Datei</div>
                    <div class="mt-1 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-medium text-slate-900 truncate">{{ $submission->original_filename }}</div>
                            <div class="text-xs text-slate-500">
                                {{ number_format($submission->file_size / 1024, 1) }} KB • {{ $submission->mime_type }}
                            </div>
                        </div>
                        <x-bladewind::button
                            tag="a"
                            href="{{ route('submissions.download', $submission) }}"
                            type="secondary"
                            size="small">
                            Herunterladen
                        </x-bladewind::button>
                    </div>
                </div>

                <div class="placeholder-file space-y-2 flex hidden align-middle py-3">
                    <div>
                        <x-bladewind::icon
                            name="document-text"
                            class="!size-14 rounded-full p-3 bg-blue-400 text-blue-100"/>
                    </div>
                    <div class="text-left pl-2.5 pt-1.5">
                        <div>Drag & Drop Dateien</div>
                        <div class="!text-xs tracking-wider opacity-70">
                            Nur <u>PDFs</u>. Max <u>20mb</u>. Leer lassen, um die jetztige Abgabe zu benützen.
                        </div>
                    </div>
                </div>

                {{-- PDF file picker --}}
                <x-bladewind::filepicker name="file" />

                <div class="flex items-center gap-3">
                    <x-bladewind::button type="primary" can_submit="true" has_spinner="true">
                        Änderungen speichern
                    </x-bladewind::button>

                    <a href="{{ route('assignments.show', $assignment) }}"
                       class="text-slate-500 hover:text-slate-700 text-sm underline">
                        Abbrechen
                    </a>
                </div>
            </form>
        </x-bladewind::card>
    </div>
</x-layout>
