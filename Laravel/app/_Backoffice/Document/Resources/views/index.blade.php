<x-bo.layout title="Documents">
    @php($current = $current?->toArray())
    <x-bo.card title="Documents par session" subtitle="Affichage à l'écran et impression A4 paysage">
        <form method="GET" class="mb-5 flex items-end gap-3" id="doc-session">
            <x-bo.select name="session" label="Session" :value="request('session', $current['id'] ?? null)"
                :options="collect($sessions->toArray())->mapWithKeys(fn ($s) => [$s['id'] => $s['titre'].' ('.$s['statut'].')'])" />
            <button class="btn-secondary">Choisir</button>
        </form>
        <ul class="divide-y divide-stone-100">
            @foreach ($documents as $n => $titre)
                <li class="flex items-center justify-between py-2.5">
                    <span><span class="mr-2 font-semibold text-stone-400">{{ $n }}.</span>{{ $titre }}</span>
                    <a class="link text-sm" href="{{ route('bo.documents.show', ['numero' => $n, 'session' => request('session', $current['id'] ?? null)]) }}">Ouvrir →</a>
                </li>
            @endforeach
        </ul>
    </x-bo.card>
</x-bo.layout>
