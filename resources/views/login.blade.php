<x-layout>

<div class="grid py-16 justify-center relative">
    <form method="post" action="/login">

        <h1 class="text-4xl p-6 text-center">Anmelden</h1>
        @csrf
        <x-bladewind::card class="min-w-md max-w-md">
            <div class="py-6">

                <x-bladewind::input
                    name="email"
                    label="E-Mail"
                    required="true"
                />
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

            </div>
            <x-bladewind::input
                type="password"
                label="Passwort"
                name="password"
                required="true"
                viewable="true"  />
            @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="text-center">
                <x-bladewind::button
                    name="btn-save"
                    has_spinner="true"
                    type="primary"
                    can_submit="true"
                    class="text-lg mt-3">
                    Anmelden
                </x-bladewind::button>


            </div>
        </x-bladewind::card>
    </form>
</div>

</x-layout>

