<x-bo.layout title="Tableau de bord">
    @php
        $session = $session?->toArray();
        $prochaine = $prochaine?->toArray();
        $nonSoldes = $nonSoldes->toArray();
    @endphp
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-bo.stat label="Membres" :value="array_sum($membresParGrade)" :href="route('bo.members.index')"
            :hint="collect($membresParGrade)->map(fn ($n, $c) => $c.' '.$n)->implode(' · ')" />
        <x-bo.stat label="Lots en vente" :value="$lotsParStatut['active'] ?? 0" :href="route('bo.products.index', ['statut' => 'active'])" />
        <x-bo.stat label="Adjugés non réglés" :value="count($nonSoldes)" :href="route('bo.products.index', ['statut' => 'awarded'])"
            :hint="($lotsParStatut['sold'] ?? 0).' achat(s) immédiat(s) inclus'" />
        <x-bo.stat label="Messages non lus" :value="$messagesNonLus" :href="route('bo.messages.index', ['membre' => auth()->id()])" />
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <x-bo.card title="Session en cours" class="lg:col-span-1">
            @if ($session)
                <p class="font-medium text-stone-900">{{ $session['titre'] }} <x-bo.status :status="$session['statut']" /></p>
                <dl class="mt-3 space-y-1 text-sm">
                    <div class="flex justify-between"><dt class="text-stone-500">Début</dt><dd>@kst($session['debut_le'])</dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Clôture (KST)</dt><dd>@kst($session['cloture_le'])</dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Temps restant</dt>
                        <dd>{{ $session['cloture_le']->isFuture() ? now()->diffForHumans($session['cloture_le'], ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) : 'terminée' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Prochaine enchère</dt><dd>@kst($prochaine['debut_le'] ?? null)</dd></div>
                </dl>
                <a href="{{ route('bo.products.index', ['session' => $session['id']]) }}" class="link mt-4 inline-block text-sm">Voir les lots →</a>
            @else
                <p class="text-sm text-stone-500">Aucune session. <a class="link" href="{{ route('bo.sessions.index') }}">Créer une session</a></p>
            @endif
        </x-bo.card>

        <x-bo.card title="Adjudications à régler" subtitle="Lots adjugés ou achetés, en attente de règlement" class="lg:col-span-2">
            @if (empty($nonSoldes))
                <p class="text-sm text-stone-500">Aucune adjudication en attente.</p>
            @else
                <table class="table">
                    <thead><tr><th>Lot</th><th>Plante</th><th>Statut</th><th class="text-right">Montant</th></tr></thead>
                    <tbody>
                    @foreach ($nonSoldes as $p)
                        <tr>
                            <td><a class="link" href="{{ route('bo.products.show', $p['id']) }}">{{ $p['numero_lot'] }}</a></td>
                            <td>{{ $p['nom'] }}</td>
                            <td><x-bo.status :status="$p['statut']" /></td>
                            <td class="text-right tabular-nums">@money($p['prix_adjudication'])</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </x-bo.card>
    </div>
</x-bo.layout>
