@php($member = $member->toArray())
<x-bo.layout :title="'Membre · '.$member['identifiant']">
    <div class="flex items-center gap-3">
        <x-bo.grade :code="$member['grade_code']" class="size-9 text-base" />
        <div>
            <p class="text-lg font-semibold text-stone-900">{{ $member['identifiant'] }} <span class="font-normal text-stone-500">· {{ $member['nom'] }}</span></p>
            <p class="text-sm text-stone-500">{{ $member['email'] }} · inscrit le @kst($member['created_at']) · dernière connexion @kst($member['derniere_connexion_le'])</p>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <x-bo.card title="Grade" class="lg:col-span-1">
            <form method="POST" action="{{ route('bo.members.grade.change', $member['id']) }}" class="flex items-end gap-2">
                @csrf
                <x-bo.select name="grade_code" label="Nouveau grade" :value="$member['grade_code']" class="flex-1"
                    :options="collect($grades->toArray())->mapWithKeys(fn ($g) => [$g['code'] => $g['code'].' · '.$g['nom']])" />
                <button class="btn">Changer</button>
            </form>
            <h3 class="mt-5 mb-2 text-xs font-medium tracking-wide text-stone-500 uppercase">Historique</h3>
            <ul class="space-y-1.5 text-sm">
                @foreach ($history->toArray() as $h)
                    <li class="flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2"><x-bo.grade :code="$h['grade_code']" /> {{ $h['grade_nom'] }}</span>
                        <span class="text-xs text-stone-500">@kst($h['created_at']) · {{ $h['edited_by'] ?? 'inscription' }}</span>
                    </li>
                @endforeach
            </ul>
            <h3 class="mt-5 mb-2 text-xs font-medium tracking-wide text-stone-500 uppercase">Parrainage</h3>
            <p class="text-sm">Parrain :
                @if ($member['parrain'])
                    <a class="link" href="{{ route('bo.members.show', $member['parrain_id']) }}">{{ $member['parrain'] }}</a>
                @else — @endif
            </p>
            @php($filleulsList = $filleuls->toArray())
            @if (! empty($filleulsList))
                <p class="mt-1 text-sm">Filleuls :
                    @foreach ($filleulsList as $f)
                        <a class="link" href="{{ route('bo.members.show', $f['id']) }}">{{ $f['identifiant'] }}</a>@if (! $loop->last), @endif
                    @endforeach
                </p>
            @endif
        </x-bo.card>

        <x-bo.card title="Informations personnelles" class="lg:col-span-2">
            <form method="POST" action="{{ route('bo.members.profil.update', $member['id']) }}" class="grid gap-4 md:grid-cols-3">
                @csrf @method('PUT')
                <x-bo.field name="nom" label="Nom" :value="$member['nom']" />
                <x-bo.field name="telephone" label="Téléphone" :value="$member['telephone']" />
                <x-bo.field name="pays" label="Pays" :value="$member['pays']" />
                <x-bo.field name="adresse" label="Adresse de livraison" type="textarea" :value="$member['adresse']" class="md:col-span-2" />
                <x-bo.select name="nationalite" label="Nationalité" :value="$member['nationalite']"
                    :options="['china' => 'Chine', 'japan' => 'Japon', 'korea' => 'Corée', 'madagascar' => 'Madagascar', 'other' => 'Autre']" />
                <div class="md:col-span-3"><button class="btn">Enregistrer le profil</button></div>
            </form>
        </x-bo.card>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <x-bo.card title="Préférences" class="lg:col-span-1">
            <form method="POST" action="{{ route('bo.members.preferences.update', $member['id']) }}" class="space-y-3">
                @csrf @method('PUT')
                <x-bo.field name="fuseau_horaire" label="Fuseau horaire" :value="$member['fuseau_horaire']" />
                @foreach (['notif_surenchere' => 'Alerte si surenchéri', 'notif_adjudication' => 'Alerte d\'adjudication', 'notif_nouveau_message' => 'Nouveau message', 'notif_prochaine_session' => 'Rappel prochaine enchère'] as $key => $label)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="{{ $key }}" value="1" @checked($member[$key]) class="rounded border-stone-300"> {{ $label }}</label>
                @endforeach
                <button class="btn">Enregistrer</button>
            </form>
        </x-bo.card>

        <x-bo.card title="Adjudications non soldées" class="lg:col-span-2">
            @forelse ($nonSoldes->toArray() as $p)
                <p class="flex justify-between border-b border-stone-100 py-1.5 text-sm last:border-0">
                    <a class="link" href="{{ route('bo.products.show', $p['id']) }}">{{ $p['numero_lot'] }} · {{ $p['nom'] }}</a>
                    <span class="tabular-nums">@money($p['prix_adjudication'])</span>
                </p>
            @empty
                <p class="text-sm text-stone-500">Aucune.</p>
            @endforelse
        </x-bo.card>
    </div>

    <x-bo.card title="Messages" subtitle="Envoyés et reçus par ce membre">
        <x-slot:actions><a class="link text-sm" href="{{ route('bo.messages.index', ['membre' => $member['id']]) }}">Tout voir →</a></x-slot:actions>
        @include('message::_table', ['messages' => $messages, 'paginate' => false])
    </x-bo.card>
</x-bo.layout>
