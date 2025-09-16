<x-layout>
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <x-bladewind::card class="p-6">
            <h1 class="text-2xl font-bold text-slate-900">Passwort zurücksetzen</h1>
            <p class="mt-1 text-slate-600">Nutzer: <span class="font-medium">{{ $user->name }}</span> ({{ $user->email }})</p>

            <form class="mt-6 space-y-6" method="POST" action="{{ route('admin.users.password.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="grid sm:grid-cols-2 gap-4">
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
                        label="(Wiederholung)"
                        required="true"
                        show_inline_error="true"
                    />
                </div>

                <div class="flex items-center gap-3">
                    <x-bladewind::button type="primary" can_submit="true" has_spinner="true">
                        Passwort setzen
                    </x-bladewind::button>
                    <a href="{{ route('admin.users.index') }}" class="text-slate-500 underline">Abbrechen</a>
                </div>
            </form>
        </x-bladewind::card>
    </div>
</x-layout>
