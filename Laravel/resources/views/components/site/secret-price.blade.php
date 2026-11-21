{{--
    Prix d'enchère masqué, version ludique : montant flouté (le « $ » reste net, on comprend tout de suite que
    c'est un prix caché), reflet de verre dépoli qui passe dessus, et un petit cactus qui monte la garde.
    Aucun vrai prix n'est transmis à ce composant : les chiffres floutés sont décoratifs.
    Bulle : un petit mot par carte (« Chut ! », « Motus ! »…), pris dans l'ordre et répété en boucle.
    Survol de la carte (classe « group » du parent) : le cactus sautille, lève le bras et affiche sa bulle.
--}}
@props(['seed' => 0, 'index' => 0])
@php
    // Faux chiffres, différents d'une carte à l'autre, pour que le flou ait l'air d'un vrai montant.
    $fake = number_format(1000 + (crc32((string) $seed) % 8999), 0, '.', "\u{202F}");
    // Décalage de la bulle « Chut ! » d'une carte à l'autre (délai négatif : déjà en cours au chargement).
    $delay = -(crc32('bubble'.$seed) % 80) / 10;
    // Petit mot de la bulle : la liste (lang/*/member.php, card.shh) est parcourue dans l'ordre des cartes, en boucle.
    $words = array_values((array) __('member.card.shh'));
    $word = $words[$index % max(1, count($words))] ?? '';
@endphp
<div style="--secret-delay: {{ $delay }}s" {{ $attributes->merge(['class' => 'secret-price flex items-center justify-between gap-2 rounded-xl bg-brand-50/70 py-1.5 pr-1.5 pl-3 ring-1 ring-brand-100']) }}>
    <p class="flex min-w-0 items-baseline gap-1.5 overflow-hidden py-1 text-xl leading-none font-bold tabular-nums" aria-hidden="true">
        <span class="secret-price__mask min-w-0">
            <span class="secret-price__digits text-stone-900 select-none">{{ $fake }}</span>
        </span>
        <span class="shrink-0 text-lg text-brand-700">$</span>
    </p>
    <span class="sr-only">{{ __('member.card.secret_sr') }}</span>

    {{-- Le gardien du prix. --}}
    <div class="secret-price__cactus relative size-10 shrink-0" aria-hidden="true">
        <span class="secret-price__bubble">{{ $word }}</span>
        <svg viewBox="0 0 48 48" class="size-full overflow-visible">
            {{-- Pot --}}
            <path d="M12 36h24l-2.5 10h-19Z" fill="#c2774a"/>
            <rect x="10.5" y="33.5" width="27" height="5" rx="2" fill="#d98a58"/>
            {{-- Bras (le gauche se lève au survol) --}}
            <g class="secret-price__arm-left">
                <path d="M16 24h-4.5a3 3 0 0 1-3-3v-5a2.5 2.5 0 0 1 5 0v3H16" fill="#4f9a5c"/>
            </g>
            <path d="M32 27h4.5a3 3 0 0 0 3-3v-4a2.5 2.5 0 0 0-5 0v2H32" fill="#4f9a5c"/>
            {{-- Corps --}}
            <rect x="15" y="9" width="18" height="27" rx="9" fill="#5fb36d"/>
            <path d="M20 13v19M24 11v22M28 13v19" stroke="#4f9a5c" stroke-width="1.2" stroke-linecap="round" opacity=".55"/>
            {{-- Fleur --}}
            <g transform="translate(24 8.5)">
                <circle r="3.2" cx="-2.6" cy="0" fill="#f28fb0"/><circle r="3.2" cx="2.6" cy="0" fill="#f28fb0"/>
                <circle r="3.2" cx="0" cy="-2.6" fill="#f6a8c2"/><circle r="2" fill="#ffd36b"/>
            </g>
            {{-- Visage : les yeux surveillent à gauche et à droite --}}
            <g class="secret-price__eyes">
                <circle cx="20.5" cy="21" r="2.1" fill="#1f2a22"/><circle cx="27.5" cy="21" r="2.1" fill="#1f2a22"/>
                <circle cx="21.2" cy="20.3" r=".7" fill="#fff"/><circle cx="28.2" cy="20.3" r=".7" fill="#fff"/>
            </g>
            <circle cx="18" cy="25" r="1.6" fill="#f28fb0" opacity=".7"/><circle cx="30" cy="25" r="1.6" fill="#f28fb0" opacity=".7"/>
            <path d="M22 26.2q2 1.6 4 0" stroke="#1f2a22" stroke-width="1.2" fill="none" stroke-linecap="round"/>
        </svg>
    </div>
</div>
