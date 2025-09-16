<x-layout>
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <x-bladewind::card class="p-6">
            <h1 class="text-2xl font-bold text-slate-900">Neuen Benutzer anlegen</h1>

            <form class="mt-6 space-y-6" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <x-bladewind::input
                    name="name"
                    label="Name"
                    required="true"
                    show_inline_error="true"
                    value="{{ old('name') }}"
                />
                <x-bladewind::input
                    name="email"
                    label="E-Mail"
                    required="true"
                    show_inline_error="true"
                    value="{{ old('email') }}"
                />
                <div class="grid sm:grid-cols-2 gap-4">
                    <x-bladewind::input
                        type="password"
                        name="password"
                        label="Passwort"
                        required="true"
                        show_inline_error="true"
                    />
                    <x-bladewind::input
                        type="password"
                        name="password_confirmation"
                        label="Passwort (Wiederholung)"
                        required="true"
                        show_inline_error="true"
                    />
                </div>

                {{-- Optional admin flag, if you store it --}}
                @if(Schema::hasColumn('users', 'is_admin'))
                    <x-bladewind::checkbox
                        name="is_admin"
                        label="Administrator/in"
                        checked="{{ old('is_admin') ? 'true' : 'false' }}"
                    />
                @endif

                <div class="flex items-center gap-3">
                    <x-bladewind::button type="primary" can_submit="true" has_spinner="true">
                        Benutzer erstellen
                    </x-bladewind::button>
                    <a href="{{ route('admin.users.index') }}" class="text-slate-500 underline">Abbrechen</a>
                </div>
            </form>
        </x-bladewind::card>
    </div>
</x-layout>
