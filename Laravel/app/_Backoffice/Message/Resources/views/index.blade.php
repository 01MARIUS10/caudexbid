<x-bo.layout title="Messagerie">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <form class="flex flex-wrap items-end gap-3">
            <x-bo.select name="type" label="Type" :value="request('type')" placeholder="Tous"
                :options="collect($types)->mapWithKeys(fn ($t) => [$t->value => $t->value])" />
            <x-bo.select name="membre" label="Membre" :value="request('membre')" placeholder="Tous" :options="$members" />
            <button class="btn-secondary">Filtrer</button>
        </form>
        <a href="{{ route('bo.messages.create') }}" class="btn">Envoyer un message</a>
    </div>
    <x-bo.card>
        @include('message::_table')
    </x-bo.card>
</x-bo.layout>
