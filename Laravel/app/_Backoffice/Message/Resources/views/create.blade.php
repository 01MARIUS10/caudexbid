<x-bo.layout title="Envoyer un message">
    <form method="POST" action="{{ route('bo.messages.store') }}">
        @csrf
        <x-bo.card title="Nouveau message" subtitle="Envoyé au nom de {{ $expediteur }}">
            <div class="grid gap-4 md:grid-cols-2">
                <x-bo.select name="destinataire_id" label="Destinataire *" placeholder="Choisir…" :options="$members" required />
                <x-bo.select name="type" label="Type" value="general" :options="collect($types)->mapWithKeys(fn ($t) => [$t->value => $t->value])" />
                <x-bo.field name="memo" label="Mémo *" type="textarea" class="md:col-span-2" required />
            </div>
            <div class="mt-4 flex gap-3">
                <button class="btn">Envoyer</button>
                <a href="{{ route('bo.messages.index') }}" class="btn-secondary">Annuler</a>
            </div>
        </x-bo.card>
    </form>
</x-bo.layout>
