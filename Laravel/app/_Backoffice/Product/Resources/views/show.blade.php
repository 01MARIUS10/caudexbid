@php($p = $product->toArray())
@php($winner = $winner?->toArray())
<x-bo.layout :title="'Lot '.$p['numero_lot']">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xl font-semibold text-stone-900">{{ $p['nom'] }} <x-bo.status :status="$p['statut']" /></p>
            <p class="text-sm text-stone-500">{{ $p['session'] }} · propriétaire : {{ $p['proprietaire'] }} · catégorie : {{ $p['categorie'] }}</p>
        </div>
        @if ($p['est_vente_non_reglee'])
            <form method="POST" action="{{ route('bo.products.settle', $p['id']) }}" onsubmit="return confirm('Confirmer le règlement ? Le lot, ses médias et ses enchères seront supprimés.')">
                @csrf
                <button class="btn-danger">Marquer comme réglé (supprime le lot)</button>
            </form>
        @endif
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <x-bo.card title="Enchère">
            <dl class="space-y-1.5 text-sm">
                <div class="flex justify-between"><dt class="text-stone-500">Prix de départ</dt><dd class="tabular-nums">@money($p['prix_depart'])</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Pas d'enchère</dt><dd class="tabular-nums">@money($p['pas_enchere'])</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Prix de vente existant</dt><dd class="tabular-nums">@money($p['prix_vente_existant'])</dd></div>
                <div class="flex justify-between font-medium"><dt>Prix actuel</dt><dd class="tabular-nums">@money($p['prix_actuel'])</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Auteur</dt><dd>{{ $p['auteur_prix_actuel'] ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Prochaine enchère min.</dt><dd class="tabular-nums">{{ $p['prochaine_enchere_minimale'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Enchérisseurs</dt><dd>{{ $p['nb_encherisseurs'] }}</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Début</dt><dd>@kst($p['debut_enchere_le'])</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Clôture prévue</dt><dd>@kst($p['cloture_prevue_le'])</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Clôture effective</dt><dd>@kst($p['cloture_effective_le'])</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Prolongations</dt><dd>{{ $p['nb_prolongations'] }} / {{ config('caudexbid.soft_close.max_extensions') }}</dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">Prix publics</dt><dd>{{ $p['prix_publics'] ? 'oui (< 48 h)' : 'non (> 48 h)' }}</dd></div>
            </dl>
        </x-bo.card>

        <x-bo.card title="Achat immédiat">
            @if ($p['achat'])
                <dl class="space-y-1.5 text-sm">
                    <div class="flex justify-between"><dt class="text-stone-500">Prix</dt><dd class="tabular-nums font-medium">@money($p['achat']['prix'])</dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Acheteur</dt><dd>{{ $p['achat']['acheteur'] ?? 'pas encore acheté' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Acheté le</dt><dd>@kst($p['achat']['achete_le'])</dd></div>
                </dl>
            @else
                <p class="text-sm text-stone-500">Ce lot ne propose pas d'achat immédiat.</p>
            @endif
            @if (in_array($p['statut'], ['draft', 'active'], true) && ! ($p['achat']['est_achete'] ?? false))
                <form method="POST" action="{{ route('bo.products.achat.define', $p['id']) }}" class="mt-4 flex items-end gap-2">
                    @csrf @method('PUT')
                    <x-bo.field name="prix" label="{{ $p['achat'] ? 'Modifier le prix' : 'Définir un prix' }}" type="number" step="0.01" :value="$p['achat']['prix'] ?? null" class="flex-1" />
                    <button class="btn">OK</button>
                </form>
            @endif
        </x-bo.card>

        <x-bo.card title="Adjudication">
            @if ($winner)
                <dl class="space-y-1.5 text-sm">
                    <div class="flex justify-between"><dt class="text-stone-500">Adjudicataire</dt><dd><a class="link" href="{{ route('bo.members.show', $winner['id']) }}">{{ $winner['identifiant'] }}</a></dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Parrain (premium)</dt><dd>{{ $winner['parrain'] ?? '—' }}</dd></div>
                    <div class="flex justify-between font-medium"><dt>Prix d'adjudication</dt><dd class="tabular-nums">@money($p['prix_adjudication'])</dd></div>
                    @if ($shares)
                        <div class="mt-2 border-t border-stone-100 pt-2 text-xs tracking-wide text-stone-500 uppercase">Répartition de la différence</div>
                        <div class="flex justify-between"><dt class="text-stone-500">Propriétaire (30 %)</dt><dd class="tabular-nums">@money($shares['proprietaire'])</dd></div>
                        <div class="flex justify-between"><dt class="text-stone-500">Membre premium (30 %)</dt><dd class="tabular-nums">@money($shares['premium'])</dd></div>
                        <div class="flex justify-between"><dt class="text-stone-500">Exploitant (40 %)</dt><dd class="tabular-nums">@money($shares['exploitant'])</dd></div>
                        <div class="flex justify-between font-medium"><dt>Dividende total propriétaire</dt><dd class="tabular-nums">@money($shares['dividende_total_proprietaire'])</dd></div>
                    @endif
                </dl>
            @else
                <p class="text-sm text-stone-500">Pas encore adjugé.</p>
            @endif
        </x-bo.card>
    </div>

    <x-bo.card title="Descriptions">
        <div class="grid gap-4 text-sm md:grid-cols-4">
            <div><p class="label">Description 1</p>{{ $p['description_1'] ?? '—' }}</div>
            <div><p class="label">Description 2</p>{{ $p['description_2'] ?? '—' }}</div>
            <div><p class="label">Description 3</p>{{ $p['description_3'] ?? '—' }}</div>
            <div><p class="label">Tailles (cm)</p>Ø {{ $p['taille_diametre'] ?? '—' }} · circ. {{ $p['taille_circonference'] ?? '—' }} · h. {{ $p['taille_hauteur'] ?? '—' }}</div>
        </div>
    </x-bo.card>

    <x-bo.card title="Photos et vidéo" subtitle="4 photos max (1 face, 2 côtés, 1 arrière) + 1 vidéo d'environ 15 s">
        <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ([1, 2, 3, 4] as $pos)
                @php($m = collect($p['medias'])->first(fn ($x) => $x['type'] === 'photo' && $x['position'] === $pos))
                <div class="rounded-lg border border-stone-200 p-2">
                    <p class="mb-1 text-xs text-stone-500">Photo {{ $pos }} @if ($m) · {{ $m['vue'] }} @if ($m['est_vitrine'])· <span class="text-brand-700">accueil</span>@endif @endif</p>
                    @if ($m)
                        <img src="{{ $m['url'] }}" alt="" class="aspect-square w-full rounded-md object-cover">
                        <form method="POST" action="{{ route('bo.media.destroy', $m['id']) }}" class="mt-1">@csrf @method('DELETE')<button class="text-xs text-red-600 hover:underline">Supprimer</button></form>
                    @else
                        <div class="flex aspect-square items-center justify-center rounded-md bg-stone-100 text-xs text-stone-400">vide</div>
                    @endif
                </div>
            @endforeach
            @php($video = collect($p['medias'])->first(fn ($x) => $x['type'] === 'video'))
            <div class="rounded-lg border border-stone-200 p-2">
                <p class="mb-1 text-xs text-stone-500">Vidéo @if ($video)· {{ $video['duree_secondes'] }} s @endif</p>
                @if ($video)
                    <video src="{{ $video['url'] }}" controls class="aspect-square w-full rounded-md bg-black object-cover"></video>
                    <form method="POST" action="{{ route('bo.media.destroy', $video['id']) }}" class="mt-1">@csrf @method('DELETE')<button class="text-xs text-red-600 hover:underline">Supprimer</button></form>
                @else
                    <div class="flex aspect-square items-center justify-center rounded-md bg-stone-100 text-xs text-stone-400">vide</div>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('bo.products.media.store', $p['id']) }}" enctype="multipart/form-data" class="mt-5 grid items-end gap-3 border-t border-stone-100 pt-4 md:grid-cols-6">
            @csrf
            <x-bo.select name="type" label="Type" value="photo" :options="['photo' => 'Photo', 'video' => 'Vidéo']" />
            <x-bo.select name="position" label="Position (photo)" :options="[1 => '1', 2 => '2', 3 => '3', 4 => '4']" />
            <x-bo.select name="vue" label="Vue (photo)" :options="['front' => 'Face', 'side' => 'Côté', 'back' => 'Arrière']" />
            <x-bo.field name="duree_secondes" label="Durée (vidéo, s)" type="number" />
            <x-bo.field name="fichier" label="Fichier" type="file" />
            <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="est_vitrine" value="1" class="rounded border-stone-300"> Écran d'accueil</label>
                <button class="btn">Téléverser</button>
            </div>
            <p class="text-xs text-stone-500 md:col-span-6">Pour une vidéo : choisir « Vidéo », indiquer la durée ; position et vue sont ignorées.</p>
        </form>
    </x-bo.card>

    <div class="grid gap-5 lg:grid-cols-3">
        <x-bo.card title="Historique des enchères" class="lg:col-span-2">
            <table class="table">
                <thead><tr><th>Date</th><th>Membre</th><th>Origine</th><th class="text-right">Montant</th><th>Prolongation</th></tr></thead>
                <tbody>
                @forelse ($bids->toArray() as $b)
                    <tr>
                        <td class="whitespace-nowrap">@kst($b['created_at'])</td>
                        <td><a class="link" href="{{ route('bo.members.show', $b['membre_id']) }}">{{ $b['membre'] }}</a></td>
                        <td><x-bo.status :status="$b['origine']" /></td>
                        <td class="text-right tabular-nums">@money($b['montant'])</td>
                        <td>{{ $b['a_prolonge'] ? '+5 min' : '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-stone-500">Aucune enchère.</td></tr>
                @endforelse
                </tbody>
            </table>
        </x-bo.card>

        <x-bo.card title="Plafonds (proxy bid)" subtitle="Confidentiels : jamais visibles des membres">
            <table class="table">
                <thead><tr><th>Membre</th><th class="text-right">Plafond</th><th>Actif</th></tr></thead>
                <tbody>
                @forelse ($proxies->toArray() as $px)
                    <tr>
                        <td>{{ $px['membre'] }}</td>
                        <td class="text-right tabular-nums">@money($px['plafond'])</td>
                        <td>{{ $px['est_actif'] ? 'oui' : 'dépassé' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-stone-500">Aucun plafond.</td></tr>
                @endforelse
                </tbody>
            </table>
        </x-bo.card>
    </div>
</x-bo.layout>
