@props(['product', 'index' => 0])
{{--
    Carte résumée d'un lot : photo 1, nom, temps restant, taille (description 1), nb d'enchérisseurs.
    Le prix d'une enchère n'est JAMAIS affiché sur la carte (ni écrit dans le HTML) : il est remplacé par
    le composant site.secret-price (étiquette floutée décorative + cactus gardien).
    Nb d'enchérisseurs : absent du tableau (ProductCardViewModel) avant les 48 h précédant la clôture.
    Lot SOLD : plus aucune information sauf le numéro de produit, mention SOLD en rouge et gras.
--}}
@php
    $p = $product;
    $sold = ($p['etat'] ?? null) === 'SOLD';
    $seconds = $p['temps_restant_secondes'] ?? 0;
    $remaining = $seconds > 0
        ? (intdiv($seconds, 86400) ? intdiv($seconds, 86400).'d ' : '').gmdate('H:i:s', $seconds % 86400)
        : __('member.card.ended');
@endphp

@if ($sold)
    <article {{ $attributes->merge(['class' => 'flex min-h-48 flex-col items-center justify-center gap-2 rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-stone-900/5']) }}>
        <p class="text-sm font-medium text-stone-500 tabular-nums">{{ __('member.card.lot', ['n' => $p['numero_lot']]) }}</p>
        <p class="text-3xl font-extrabold tracking-[0.15em] text-red-600">SOLD</p>
    </article>
@else
<article {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-stone-900/5 transition hover:shadow-md']) }}>
    <div class="relative aspect-square overflow-hidden bg-stone-100">
        @if (! empty($p['photo_1']))
            <img src="{{ $p['photo_1'] }}" alt="{{ $p['nom'] }}" loading="lazy" decoding="async"
                 class="size-full object-cover transition duration-700 ease-out group-hover:scale-[1.04]">
        @else
            <div class="flex size-full items-center justify-center">
                <svg viewBox="0 0 24 24" class="size-10 text-stone-300" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 21c-1.6-3.6.6-7 5.5-7s7.1 3.4 5.5 7Z"/><path d="M12 14V8"/><path d="M12 10.5c-2-.6-3.4-2.3-3.6-4.5"/><path d="M12 9c1.8-.4 3.2-1.8 3.8-4"/></svg>
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-2 p-3.5 md:p-4">
            <h3 class="truncate text-[15px] font-medium text-stone-900" title="{{ $p['nom'] }}">{{ $p['nom'] }}</h3>

            {{-- Le prix d'une enchère n'est jamais affiché sur la carte : petit gardien à la place. --}}
            <x-site.secret-price :seed="$p['id']" :index="$index" />

            @if (filled($p['taille'] ?? null))
                <p class="line-clamp-2 text-sm text-stone-600"><span class="text-stone-400">Size</span> {{ $p['taille'] }}</p>
            @endif

            <div class="mt-auto flex items-center justify-between gap-3 border-t border-stone-100 pt-2.5 text-sm text-stone-600">
                <span class="flex min-w-0 items-center gap-1.5" title="{{ __('member.card.closing', ['date' => $p['cloture_le_kst']]) }}">
                    <svg viewBox="0 0 24 24" class="size-4 shrink-0 text-stone-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <span class="truncate tabular-nums"
                          @if ($seconds > 0) data-countdown="{{ $p['cloture_le'] }}" data-countdown-end="{{ __('member.card.ended') }}" @endif>{{ $remaining }}</span>
                </span>
                @isset($p['nb_encherisseurs'])
                    <span class="flex shrink-0 items-center gap-1.5" title="{{ __('member.card.bidders') }}">
                        {{-- Icône « nombre d'enchérisseurs » : marteau d'enchère. --}}
                        <svg viewBox="0 0 24 24" class="size-4 text-stone-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m14.5 12.5-8 8a2.12 2.12 0 1 1-3-3l8-8"/><path d="m16 16 6-6"/><path d="m8 8 6-6"/><path d="m9 7 8 8"/><path d="m21 11-8-8"/></svg>
                        <span class="font-medium tabular-nums">{{ $p['nb_encherisseurs'] }}</span>
                        <span class="sr-only">{{ __('member.card.bidders') }}</span>
                    </span>
                @endisset
            </div>
    </div>
</article>
@endif
