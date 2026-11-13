<x-bo.layout title="Enchères">
    <form class="flex flex-wrap items-end gap-3">
        <x-bo.select name="session" label="Session" :value="request('session')" placeholder="Toutes"
            :options="collect($sessions->toArray())->mapWithKeys(fn ($s) => [$s['id'] => $s['titre']])" />
        <x-bo.select name="membre" label="Membre" :value="request('membre')" placeholder="Tous" :options="$members" />
        <button class="btn-secondary">Filtrer</button>
    </form>
    <x-bo.card>
        <table class="table">
            <thead><tr><th>Date (KST)</th><th>Lot</th><th>Plante</th><th>Membre</th><th>Origine</th><th class="text-right">Montant</th><th>Prolongation</th></tr></thead>
            <tbody>
            @forelse ($bids->toArray() as $b)
                <tr>
                    <td class="whitespace-nowrap">@kst($b['created_at'])</td>
                    <td><a class="link" href="{{ route('bo.products.show', $b['produit_id']) }}">{{ $b['numero_lot'] }}</a></td>
                    <td>{{ $b['plante'] }}</td>
                    <td><a class="link" href="{{ route('bo.members.show', $b['membre_id']) }}">{{ $b['membre'] }}</a></td>
                    <td><x-bo.status :status="$b['origine']" /></td>
                    <td class="text-right tabular-nums">@money($b['montant'])</td>
                    <td>{{ $b['a_prolonge'] ? '+5 min' : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-stone-500">Aucune enchère.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $bids->links() }}</div>
    </x-bo.card>
</x-bo.layout>
