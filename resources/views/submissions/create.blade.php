<x-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Flash messages --}}
        @if(session('success'))
            <x-bladewind::notification type="success" show_close_icon="true" class="mb-6">
                {{ session('success') }}
            </x-bladewind::notification>
        @endif
        @if(session('error'))
            <x-bladewind::notification type="error" show_close_icon="true" class="mb-6">
                {{ session('error') }}
            </x-bladewind::notification>
        @endif



        <x-bladewind::card class="p-6">
            <h1 class="text-2xl font-bold text-slate-900">Abgabe einreichen</h1>
            <p class="mt-1 text-slate-600">Lade dein PDF hoch. Kein Account erforderlich.</p>
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <p class="font-semibold">Bitte korrigiere die folgenden Fehler:</p>
                    <ul class="mt-2 list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="mt-6 space-y-6"
                  method="POST"
                  action="{{ route('assignments.submit') }}"
                  enctype="multipart/form-data">
                @csrf

                {{-- Assignment fixed vs. code input --}}
                @if($assignment != null)
                    <input type="hidden" name="code" value="{{ $assignment->code }}">

                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Aufgabencode</label>
                        <div class="rounded-xl border border-slate-200 bg-white p-4 flex items-center justify-between gap-3">
                            <div>
                                <div class="font-medium text-slate-900">{{ $assignment->name }}</div>
                                @if(!empty($assignment->description))
                                    <div class="text-sm text-slate-600 line-clamp-2">{{ $assignment->description }}</div>
                                @endif
                            </div>
                            <x-bladewind::tag color="blue">Code: {{ $assignment->code }}</x-bladewind::tag>
                        </div>
                    </div>
                @else
                    <x-bladewind::input
                        name="code"
                        label="Aufgabencode"
                        required="true"
                        show_inline_error="true"
                        value="{{ old('code') }}"
                    />
                @endif

                <x-bladewind::input
                    name="student_name"
                    label="Name"
                    required="true"
                    show_inline_error="true"
                    value="{{ old('student_name') }}"
                />

                <div class="placeholder-file space-y-2 flex hidden align-middle py-3">
                    <div>
                        <x-bladewind::icon
                            name="document-text"
                            class="!size-14 rounded-full p-3 bg-blue-400 text-blue-100"/>
                    </div>
                    <div class="text-left pl-2.5 pt-1.5">
                        <div>Drag & Drop Dateien</div>
                        <div class="!text-xs tracking-wider opacity-70">
                            Nur <u>PDFs</u>. Max <u>20mb</u>
                        </div>
                    </div>
                </div>

                {{-- PDF file picker --}}
                <x-bladewind::filepicker name="file" />


                <div class="flex items-center gap-3 mt-8">
                    <x-bladewind::button type="primary" can_submit="true" has_spinner="true">
                        Abgabe hochladen
                    </x-bladewind::button>

                    <a href="{{ url()->previous() }}"
                       class="text-slate-500 hover:text-slate-700 text-sm underline">
                        Abbrechen
                    </a>
                </div>

                <p class="text-xs text-slate-500">
                    Dateien werden sicher gespeichert. Lehrkräfte sehen nur Abgaben zur jeweiligen Aufgabe.
                </p>
            </form>
        </x-bladewind::card>
    </div>
</x-layout>
