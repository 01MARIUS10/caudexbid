<x-bo.layout title="Nouveau membre">
    <form method="POST" action="{{ route('bo.members.store') }}" class="space-y-5">
        @csrf
        <x-bo.card title="Compte">
            <div class="grid gap-4 md:grid-cols-3">
                <x-bo.field name="identifiant" label="Identifiant *" required />
                <x-bo.field name="email" label="E-mail *" type="email" required />
                <x-bo.field name="password" label="Mot de passe *" type="password" required hint="8 caractères minimum" />
                <x-bo.select name="grade_code" label="Grade *" value="G"
                    :options="collect($grades->toArray())->mapWithKeys(fn ($g) => [$g['code'] => $g['code'].' · '.$g['nom']])" />
                <x-bo.select name="parrain_id" label="Parrain (premium)" placeholder="Aucun" :options="$premiums" />
            </div>
        </x-bo.card>
        <x-bo.card title="Coordonnées">
            <div class="grid gap-4 md:grid-cols-3">
                <x-bo.field name="nom" label="Nom" />
                <x-bo.field name="telephone" label="Téléphone" />
                <x-bo.field name="pays" label="Pays" />
                <x-bo.field name="adresse" label="Adresse de livraison" type="textarea" class="md:col-span-3" />
                <x-bo.select name="nationalite" label="Nationalité *" value="other"
                    :options="['china' => 'Chine', 'japan' => 'Japon', 'korea' => 'Corée', 'madagascar' => 'Madagascar', 'other' => 'Autre']" />
            </div>
        </x-bo.card>
        <div class="flex gap-3">
            <button class="btn">Créer le membre</button>
            <a href="{{ route('bo.members.index') }}" class="btn-secondary">Annuler</a>
        </div>
    </form>
</x-bo.layout>
