<x-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <x-bladewind::card class="p-8 text-center">

            {{-- Icon --}}
            <div class="mx-auto mb-4 h-16 w-16 flex items-center justify-center rounded-full bg-emerald-100">
                <svg class="h-10 w-10 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            {{-- Heading --}}
            <h1 class="text-2xl font-bold text-slate-900">Herzlichen Glückwunsch! 🎉</h1>
            <p class="mt-2 text-slate-600">Deine Abgabe wurde erfolgreich eingereicht.</p>

            {{-- Assignment info --}}
            <div class="mt-8 text-left space-y-4">
                <div>
                    <p class="text-sm text-slate-500">Aufgabe</p>
                    <p class="text-lg font-medium text-slate-800">
                        {{ $request->assignment }}
                    </p>
                    <p class="text-sm text-slate-500">von</p>
                    <p class="text-lg font-medium text-slate-800">
                        {{ $request->author }}
                    </p>
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <p class="text-sm text-slate-500">Deine Abgabe</p>
                    <p class="font-medium text-slate-800">
                        Name: {{ $request->student_name }}
                    </p>
                    <p class="text-sm text-slate-600">
                        Datei: {{ $request->file_name }}<br>
                    </p>
                    <p class="text-sm text-slate-600">
                        am: {{ $request->submitted_at }}<br>
                    </p>
                </div>
            </div>

            {{-- Button --}}
            <div class="mt-8">
                <x-bladewind::button tag="a" href="{{ route('welcome') }}" type="primary">
                    Zur Startseite
                </x-bladewind::button>
            </div>
        </x-bladewind::card>
    </div>
</x-layout>
