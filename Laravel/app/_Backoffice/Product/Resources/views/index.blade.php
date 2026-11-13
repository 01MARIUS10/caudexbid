<x-bo.layout title="Produits">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <form class="flex flex-wrap items-end gap-3">
            <x-bo.field name="q" label="Recherche" :value="request('q')" placeholder="Lot ou nom" />
            <x-bo.select name="session" label="Session" :value="request('session')" placeholder="Toutes"
                :options="collect($sessions->toArray())->mapWithKeys(fn ($s) => [$s['id'] => $s['titre']])" />
            <x-bo.select name="statut" label="Statut" :value="request('statut')" placeholder="Tous"
                :options="collect($statuts)->mapWithKeys(fn ($s) => [$s->value => $s->value])" />
            <button class="btn-secondary">Filtrer</button>
        </form>
        <a href="{{ route('bo.products.create') }}" class="btn">Nouveau lot</a>
    </div>

    <x-bo.card>
        <table class="table">
            <thead><tr><th></th><th>Lot</th><th>Plante</th><th>Session</th><th>Statut</th><th class="text-right">Départ</th><th class="text-right">Prix actuel</th><th class="text-right">Enchér.</th><th class="text-right">Achat imm.</th><th>Clôture (KST)</th></tr></thead>
            <tbody>
            @forelse ($products->toArray() as $p)
                <tr>
                    <td class="w-12">@if ($p['photo_1'])<img src="{{ $p['photo_1'] }}" alt="" class="size-10 rounded-md object-cover">@endif</td>
                    <td><a class="link" href="{{ route('bo.products.show', $p['id']) }}">{{ $p['numero_lot'] }}</a></td>
                    <td>{{ $p['nom'] }}<div class="text-xs text-stone-500">{{ $p['description_1'] }}</div></td>
                    <td class="whitespace-nowrap">{{ $p['session'] }}</td>
                    <td><x-bo.status :status="$p['statut']" /></td>
                    <td class="text-right tabular-nums">@money($p['prix_depart'])</td>
                    <td class="text-right tabular-nums">@money($p['prix_actuel'])<div class="text-xs text-stone-500">{{ $p['auteur_prix_actuel'] }}</div></td>
                    <td class="text-right tabular-nums">{{ $p['nb_encherisseurs'] }}</td>
                    <td class="text-right tabular-nums">@money($p['achat']['prix'] ?? null)</td>
                    <td class="whitespace-nowrap">@kst($p['cloture_effective_le'])@if ($p['nb_prolongations'])<div class="text-xs text-amber-700">+{{ $p['nb_prolongations'] * 5 }} min</div>@endif</td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-stone-500">Aucun lot.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $products->links() }}</div>
    </x-bo.card>
</x-bo.layout>
