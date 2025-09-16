<x-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

        @if ($errors->any())
            <x-bladewind::card type="error" show_close_icon="true" class="mb-2">
                <div class="font-semibold">Bitte korrigiere die folgenden Fehler:</div>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            </x-bladewind::card>
        @endif

        {{-- Profile card --}}
        <x-bladewind::card class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Dein Profil</h1>
                    <p class="mt-1 text-slate-600">Passe deinen Namen und deine E-Mail an.</p>
                </div>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('profile.update') }}">
                @csrf

                <div class="grid sm:grid-cols-2 gap-6">
                    <x-bladewind::input
                        name="name"
                        label="Name"
                        required="true"
                        show_inline_error="true"
                        value="{{ old('name', $user->name) }}"
                    />

                    <x-bladewind::input
                        name="email"
                        label="E-Mail"
                        required="true"
                        show_inline_error="true"
                        value="{{ old('email', $user->email) }}"
                    />
                </div>

                <div class="flex items-center gap-3">
                    <x-bladewind::button type="primary" can_submit="true" has_spinner="true">
                        Profil speichern
                    </x-bladewind::button>
                </div>
            </form>
        </x-bladewind::card>

        {{-- Password card --}}
        <x-bladewind::card class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Passwort ändern</h2>
                    <p class="mt-1 text-slate-600">Setze ein neues, sicheres Passwort.</p>
                </div>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('password.change') }}">
                @csrf

                <x-bladewind::input
                    type="password"
                    name="current_password"
                    label="Aktuelles Passwort"
                    required="true"
                    show_inline_error="true"
                />

                <div class="grid sm:grid-cols-2 gap-6">
                    <x-bladewind::input
                        type="password"
                        name="password"
                        label="Neues Passwort"
                        required="true"
                        show_inline_error="true"
                    />
                    <x-bladewind::input
                        type="password"
                        name="password_confirmation"
                        label="Neues Passwort (Wiederholung)"
                        required="true"
                        show_inline_error="true"
                    />
                </div>

                <div class="flex items-center gap-3">
                    <x-bladewind::button type="primary" can_submit="true" has_spinner="true">
                        Passwort ändern
                    </x-bladewind::button>
                </div>
            </form>
        </x-bladewind::card>

        @if(Auth::user() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin())
        {{-- Admin: User Management --}}
        <x-bladewind::card class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Benutzerverwaltung</h2>
                    <p class="mt-1 text-slate-600">Als Admin kannst du Nutzer anlegen und Passwörter zurücksetzen.</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <x-bladewind::button tag="a" href="{{ route('admin.users.index') }}" type="secondary">
                    Alle Benutzer
                </x-bladewind::button>

                <x-bladewind::button tag="a" href="{{ route('admin.users.create') }}" type="primary">
                    Neuen Benutzer anlegen
                </x-bladewind::button>
            </div>
        </x-bladewind::card>
        @endif
    </div>
</x-layout>
