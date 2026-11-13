<x-bo.layout title="Membres">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <form class="flex flex-wrap items-end gap-3">
            <x-bo.field name="q" label="Recherche" :value="request('q')" placeholder="Identifiant, e-mail, nom" />
            <x-bo.select name="grade" label="Grade" :value="request('grade')" placeholder="Tous"
                :options="collect($grades->toArray())->mapWithKeys(fn ($g) => [$g['code'] => $g['code'].' · '.$g['nom']])" />
            <button class="btn-secondary">Filtrer</button>
        </form>
        <a href="{{ route('bo.members.create') }}" class="btn">Nouveau membre</a>
    </div>

    <x-bo.card>
        <table class="table">
            <thead><tr><th>Grade</th><th>Identifiant</th><th>Nom</th><th>E-mail</th><th>Nationalité</th><th>Parrain</th><th>Dernière connexion</th></tr></thead>
            <tbody>
            @forelse ($members->toArray() as $m)
                <tr>
                    <td><x-bo.grade :code="$m['grade_code']" /></td>
                    <td><a class="link" href="{{ route('bo.members.show', $m['id']) }}">{{ $m['identifiant'] }}</a></td>
                    <td>{{ $m['nom'] }}</td>
                    <td>{{ $m['email'] }}</td>
                    <td>{{ $m['nationalite'] }}</td>
                    <td>{{ $m['parrain'] ?? '—' }}</td>
                    <td>@kst($m['derniere_connexion_le'])</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-stone-500">Aucun membre.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $members->links() }}</div>
    </x-bo.card>
</x-bo.layout>
