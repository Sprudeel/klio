<x-layout>
    <section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Neue Aufgabe anlegen</h1>
            <p class="mt-2 text-slate-600">Erstelle eine neue Abgabe mit Code und Deadline.</p>
        </div>

        {{-- Validation errors --}}
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

        <form method="POST" action="{{ route('assignments.update', $assignment) }}" class="space-y-6">
            @csrf

            <x-bladewind::card class="p-6">
                <div class="grid gap-5 sm:grid-cols-2">
                    {{-- Name --}}
                    <div class="sm:col-span-2">
                        <x-bladewind::input
                            name="name"
                            label="Titel der Aufgabe"
                            placeholder="z. B. Aufsatz: Erörterung"
                            required="true"
                            show_inline_error="true"
                            value="{{ $assignment->name }}"
                            error_message="@error('name'){{ $errors->first('name') }}@enderror"
                        />
                    </div>

                    {{-- Code (8 chars) --}}
                    <div>
                        <x-bladewind::input
                            name="code"
                            label="Code (8 Zeichen)"
                            placeholder="z. B. 4gerört"
                            maxlength="8"
                            required="true"
                            show_inline_error="true"
                            value="{{ $assignment->code }}"
                            error_message="@error('code'){{$errors->first('code') }}@enderror"
                        />
                        <p class="mt-1 text-xs text-slate-500">Teile diesen Code mit deinen Schüler:innen. Wenn du dieses Feld leer lässt wird automatisch ein Code erstellt.</p>
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <x-bladewind::input
                            type="date"
                            name="deadline"
                            label="Deadline"
                            required="true"
                            show_inline_error="true"
                            value="{{ ($assignment->deadline)->format('Y-m-d') }}"
                            error_message="@error('deadline'){{$errors->first('deadline') }}@enderror"
                        />
                    </div>

                    {{-- Color --}}
                    <div>
                        <div class="flex gap-8">
                            <label class="bw-label ">Farbe</label>
                            <input type="color" name="color"
                                   value="{{ $assignment->color }}"
                                   class="bw-input h-10 w-16 p-1"
                            /></div>
                        @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('color') }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-500">Wird zur Hervorhebung in Listen genutzt.</p>
                    </div>

                    {{-- Icon --}}
                    <div>
                        <x-bladewind::input
                            name="icon"
                            label="Icon"
                            placeholder="z. B. ✍️ "
                            show_inline_error="true"
                            value="{{ $assignment->icon }}"
                            error_message="@error('icon'){{ $errors->first('icon') }}@enderror"
                        />
                        <p class="mt-1 text-xs text-slate-500">Du kannst ein Emoji oder einen Icon-Key verwenden.</p>
                    </div>

                    {{-- Beschreibung (optional) --}}
                    <div class="sm:col-span-2">
                        <x-bladewind::textarea
                            name="description"
                            label="Beschreibung (optional)"
                            placeholder="Kurze Hinweise für die Abgabe …"
                        >{{ $assignment->description }}</x-bladewind::textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('description') }}</p>
                        @enderror
                    </div>
                </div>
            </x-bladewind::card>

            <div class="flex items-center justify-end gap-8">
                <a href="{{ url()->previous() }}"
                   class="text-slate-600 hover:text-slate-900 underline underline-offset-4">
                    Abbrechen
                </a>

                <x-bladewind::button
                    type="primary"
                    has_spinner="true"
                    can_submit="true">
                    Bearbeiten
                </x-bladewind::button>
            </div>
        </form>
    </section>
</x-layout>
