<x-bo.layout title="Propriétaires des plantes">
    <div class="grid gap-5 lg:grid-cols-3">
        <x-bo.card title="Propriétaires / pépinières" class="lg:col-span-2">
            <table class="table">
                <thead><tr><th>Nom</th><th>Pays</th><th>E-mail</th><th>Infos de versement</th><th class="text-right">Lots</th></tr></thead>
                <tbody>
                @forelse ($owners->toArray() as $o)
                    <tr>
                        <td class="font-medium">{{ $o['nom'] }}</td>
                        <td>{{ $o['pays'] }}</td>
                        <td>{{ $o['email'] }}</td>
                        <td class="text-xs text-stone-500">{{ $o['infos_bancaires'] }}</td>
                        <td class="text-right tabular-nums">{{ $o['products_count'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-stone-500">Aucun propriétaire.</td></tr>
                @endforelse
                </tbody>
            </table>
        </x-bo.card>

        <x-bo.card title="Ajouter un propriétaire">
            <form method="POST" action="{{ route('bo.owners.store') }}" class="space-y-4">
                @csrf
                <x-bo.field name="nom" label="Nom *" required />
                <x-bo.field name="pays" label="Pays" value="Madagascar" />
                <x-bo.field name="email" label="E-mail" type="email" />
                <x-bo.field name="infos_bancaires" label="Infos de versement" type="textarea" />
                <button class="btn">Ajouter</button>
            </form>
        </x-bo.card>
    </div>
</x-bo.layout>
