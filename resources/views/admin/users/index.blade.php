<x-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">

        @if(session('success'))
            <x-bladewind::notification type="success" show_close_icon="true">
                {{ session('success') }}
            </x-bladewind::notification>
        @endif

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">Benutzerverwaltung</h1>
            <x-bladewind::button tag="a" href="{{ route('admin.users.create') }}" type="primary">
                Neuen Benutzer anlegen
            </x-bladewind::button>
        </div>

        <form method="GET" class="flex gap-2">
            <x-bladewind::input
                name="q"
                placeholder="Suche nach Name oder E-Mail"
                value="{{ $q }}"
            />
            <x-bladewind::button type="primary" can_submit="true">Suchen</x-bladewind::button>
        </form>

        <x-bladewind::table>
            <x-slot name="header">
                <th>Name</th>
                <th>E-Mail</th>
                <th class="text-right">Aktionen</th>
            </x-slot>

            @foreach($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td class="text-slate-600">{{ $u->email }}</td>
                    <td class="text-right">
                        <x-bladewind::button tag="a"
                                             href="{{ route('admin.users.password.edit', $u) }}"
                                             type="secondary"
                                             size="small">
                            Passwort zurücksetzen
                        </x-bladewind::button>
                    </td>
                </tr>
            @endforeach
        </x-bladewind::table>

        <div>
            {{ $users->links() }}
        </div>
    </div>
</x-layout><?php
