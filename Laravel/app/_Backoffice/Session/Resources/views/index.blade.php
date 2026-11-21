<x-bo.layout title="Sessions des enchères">
    <div class="grid gap-5 lg:grid-cols-3">
        <x-bo.card title="Liste des sessions" class="lg:col-span-2">
            <table class="table">
                <thead><tr><th>Titre</th><th>Début (KST)</th><th>Clôture (KST)</th><th>Statut</th><th class="text-right">Lots</th></tr></thead>
                <tbody>
                @forelse ($sessions->toArray() as $s)
                    <tr>
                        <td class="font-medium">{{ $s['titre'] }}</td>
                        <td>@kst($s['debut_le'])</td>
                        <td>@kst($s['cloture_le'])</td>
                        <td><x-bo.status :status="$s['statut']" /></td>
                        <td class="text-right"><a class="link" href="{{ route('bo.products.index', ['session' => $s['id']]) }}">{{ $s['products_count'] }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-stone-500">Aucune session.</td></tr>
                @endforelse
                </tbody>
            </table>
        </x-bo.card>

        <x-bo.card title="Nouvelle session" subtitle="Durée 3 à 5 jours · clôture conseillée le dimanche 20h-22h KST">
            <form method="POST" action="{{ route('bo.sessions.store') }}" class="space-y-4">
                @csrf
                <x-bo.field name="titre" label="Titre" placeholder="Session décembre 2026" />
                <x-bo.field name="debut_le" label="Début (heure KST) *" type="datetime-local" required />
                <x-bo.field name="cloture_le" label="Clôture (heure KST) *" type="datetime-local" required />
                <button class="btn">Créer la session</button>
            </form>
        </x-bo.card>
    </div>
</x-bo.layout>
