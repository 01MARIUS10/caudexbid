<x-bo.layout title="Nouveau lot">
    @php
        $plantList = collect($plants->toArray())->keyBy('id');
        // Plante choisie (lien « Créer un lot » de la collection) : valeurs par défaut des champs, modifiables.
        $defaults = $plantList->get(old('plante_id', $selected)) ?? [];
    @endphp
    <form method="POST" action="{{ route('bo.products.store') }}" class="space-y-5">
        @csrf
        <x-bo.card title="Plante" subtitle="Facultatif : choisir une plante de la collection pré-remplit le nom, modifiable avant d'enregistrer">
            <div class="flex flex-wrap items-end gap-3">
                <x-bo.select name="plante_id" label="Plante de la collection" placeholder="Aucune" :value="old('plante_id', $selected)" class="min-w-72"
                    :options="$plantList->mapWithKeys(fn ($p) => [$p['id'] => $p['nom'].($p['genre'] ? ' ('.$p['genre'].')' : '')])" />
                <a href="{{ route('bo.plants.index') }}" class="link pb-2 text-sm">Gérer la collection</a>
            </div>
        </x-bo.card>

        <x-bo.card title="Lot" subtitle="Les dates d'enchère sont celles de la session">
            <div class="grid gap-4 md:grid-cols-3">
                <x-bo.select name="session_enchere_id" label="Session *" placeholder="Choisir…" required
                    :options="collect($sessions->toArray())->mapWithKeys(fn ($s) => [$s['id'] => $s['titre'].' ('.$s['statut'].')'])" />
                <x-bo.select name="proprietaire_id" label="Propriétaire *" placeholder="Choisir…" required
                    :options="collect($owners->toArray())->mapWithKeys(fn ($o) => [$o['id'] => $o['nom']])" />
                <x-bo.field name="numero_lot" label="Numéro de lot *" required placeholder="2611-11" />
                <x-bo.field name="nom" label="Nom de la plante *" required class="md:col-span-2" :value="$defaults['nom'] ?? null" />
                <x-bo.field name="categorie" label="Catégorie" value="plant" />
                <x-bo.field name="description_1" label="Description 1 (taille, affichée sur la carte)" type="textarea" />
                <x-bo.field name="description_2" label="Description 2" type="textarea" />
                <x-bo.field name="description_3" label="Description 3" type="textarea" />
                <x-bo.field name="taille_diametre" label="Diamètre (cm)" type="number" step="0.01" />
                <x-bo.field name="taille_circonference" label="Circonférence (cm)" type="number" step="0.01" />
                <x-bo.field name="taille_hauteur" label="Hauteur (cm)" type="number" step="0.01" />
            </div>
        </x-bo.card>
        <x-bo.card title="Prix">
            <div class="grid gap-4 md:grid-cols-4">
                <x-bo.field name="prix_vente_existant" label="Prix de vente existant ($)" type="number" step="0.01" />
                <x-bo.field name="prix_depart" label="Prix de départ ($) *" type="number" step="0.01" required />
                <x-bo.field name="pas_enchere" label="Pas d'enchère ($) *" type="number" step="0.01" required />
                <x-bo.field name="prix_achat_immediat" label="Prix d'achat immédiat ($)" type="number" step="0.01" hint="Laisser vide si pas d'achat immédiat" />
            </div>
            <label class="mt-4 flex items-center gap-2 text-sm"><input type="checkbox" name="publier" value="1" checked class="rounded border-stone-300"> Publier le lot (statut « En vente »)</label>
        </x-bo.card>
        <div class="flex gap-3">
            <button class="btn">Créer le lot</button>
            <a href="{{ route('bo.products.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>

    {{-- Choix d'une plante : remplit le nom (toujours modifiable). --}}
    <script>
        (() => {
            const plants = @json($plantList->map(fn ($p) => \Illuminate\Support\Arr::only($p, \App\Modules\Product\_ProductPlant\Models\ProductPlant::DEFAULT_FIELDS)));
            document.getElementById('plante_id').addEventListener('change', (e) => {
                const plant = plants[e.target.value];
                if (!plant) return;
                Object.entries(plant).forEach(([field, value]) => {
                    const input = document.getElementById(field);
                    if (input) input.value = value ?? '';
                });
            });
        })();
    </script>
</x-bo.layout>
