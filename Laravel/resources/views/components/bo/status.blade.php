@props(['status'])
@php
    $value = $status instanceof \BackedEnum ? $status->value : $status;
    [$label, $class] = match ($value) {
        'draft' => ['Brouillon', 'bg-stone-100 text-stone-600'],
        'active' => ['En vente', 'bg-sky-100 text-sky-700'],
        'sold' => ['SOLD · achat immédiat', 'bg-red-100 text-red-700 font-bold'],
        'awarded' => ['Adjugé', 'bg-amber-100 text-amber-800'],
        'unsold' => ['Non vendu', 'bg-stone-100 text-stone-500'],
        'scheduled' => ['Programmée', 'bg-stone-100 text-stone-600'],
        'open' => ['Ouverte', 'bg-emerald-100 text-emerald-700'],
        'closed' => ['Clôturée', 'bg-amber-100 text-amber-800'],
        'settled' => ['Réglée', 'bg-stone-100 text-stone-500'],
        'manual' => ['Manuelle', 'bg-stone-100 text-stone-600'],
        'proxy' => ['Automatique', 'bg-violet-100 text-violet-700'],
        'general' => ['Général', 'bg-stone-100 text-stone-600'],
        'regular_member_request' => ['Demande statut M', 'bg-amber-100 text-amber-800'],
        'registration' => ['Enregistrement', 'bg-sky-100 text-sky-700'],
        default => [$value, 'bg-stone-100 text-stone-600'],
    };
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium whitespace-nowrap $class"]) }}>{{ $label }}</span>
