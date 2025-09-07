{{-- resources/views/assignments/create.blade.php --}}
<x-auth-layout>
    <section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Aufgabe {{ $assignment }}</h1>
            <p class="mt-2 text-slate-600">Submissions: {{ $submissions  }}</p>
        </div>


    </section>
</x-auth-layout>
