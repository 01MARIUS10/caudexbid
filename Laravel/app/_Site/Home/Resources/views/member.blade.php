<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>caudexbid · {{ __('member.nav.current_bids') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&display=swap">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-800 antialiased">
    @php
        // Menu membre (cahier des charges, « après connexion »). '#' = écran pas encore codé.
        $menu = [
            ['label' => __('member.nav.current_bids'), 'href' => route('site.home'), 'active' => true,
             'icon' => '<path d="m14.5 12.5-8 8a2.12 2.12 0 1 1-3-3l8-8"/><path d="m16 16 6-6"/><path d="m8 8 6-6"/><path d="m9 7 8 8"/><path d="m21 11-8-8"/>'],
            ['label' => __('member.nav.unsettled'), 'href' => '#',
             'icon' => '<path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
            ['label' => __('member.nav.inbox'), 'href' => '#', 'badge' => $user['unread'],
             'icon' => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>'],
            ['label' => __('member.nav.outbox'), 'href' => '#',
             'icon' => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>'],
            ['label' => __('member.nav.profile'), 'href' => '#',
             'icon' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>'],
        ];
        // Changement de grade (G <-> M) des membres dont on est le parrain : visible pour P et A uniquement.
        if ($user['can_change_grade']) {
            $menu[] = ['label' => __('member.nav.grade'), 'href' => '#',
                       'icon' => '<path d="m17 11-5-5-5 5"/><path d="m17 18-5-5-5 5"/>'];
        }
    @endphp

    <div class="mx-auto flex min-h-screen max-w-[1440px] flex-col md:flex-row">

        {{-- Colonne de gauche : membre (identifiant + grade), menu, langue. --}}
        <aside class="z-10 shrink-0 border-b border-stone-200 bg-white md:sticky md:top-0 md:h-screen md:w-64 md:overflow-y-auto md:border-r md:border-b-0">
            <div class="flex h-full flex-col gap-4 px-5 py-4 md:gap-8 md:px-5 md:py-9">
                <div class="flex items-center justify-between gap-4 md:px-2">
                    <x-site.brand />
                    <form method="POST" action="{{ route('site.logout') }}" class="md:hidden">
                        @csrf
                        <button class="rounded-lg px-2.5 py-1.5 text-sm text-stone-500 hover:bg-stone-100 hover:text-stone-900">{{ __('home.logout') }}</button>
                    </form>
                </div>

                <div class="flex items-center gap-3 rounded-xl bg-stone-50 px-3 py-2.5 ring-1 ring-stone-200/70">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-900 uppercase">{{ mb_substr($user['identifiant'], 0, 1) }}</span>
                    <span class="min-w-0">
                        <span class="flex items-center gap-1.5">
                            <span class="truncate font-semibold text-stone-900">{{ $user['identifiant'] }}</span>
                            <x-site.grade :code="$user['grade']" />
                        </span>
                        <span class="block text-xs text-stone-500">{{ __('member.grades.'.$user['grade']) }}</span>
                    </span>
                </div>

                <nav aria-label="{{ __('home.account') }}" class="-mx-5 overflow-x-auto px-5 md:mx-0 md:overflow-visible md:px-0">
                    <ul class="flex gap-1.5 md:flex-col md:gap-0.5">
                        @foreach ($menu as $item)
                            <li class="shrink-0">
                                <a href="{{ $item['href'] }}" @if ($item['active'] ?? false) aria-current="page" @endif
                                   @class([
                                       'flex items-center gap-3 rounded-lg px-3 py-2 text-sm leading-snug whitespace-nowrap transition md:whitespace-normal',
                                       'bg-brand-50 font-semibold text-brand-900 ring-1 ring-brand-100' => $item['active'] ?? false,
                                       'text-stone-600 hover:bg-stone-100 hover:text-stone-900' => ! ($item['active'] ?? false),
                                   ])>
                                    <svg viewBox="0 0 24 24" class="size-4 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['icon'] !!}</svg>
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if (! empty($item['badge']))
                                        <span class="rounded-full bg-brand-600 px-1.5 py-0.5 text-[11px] leading-none font-semibold text-white tabular-nums">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="hidden space-y-1 md:mt-auto md:block">
                    @if ($user['is_admin'])
                        <a href="{{ route('bo.dashboard') }}" class="btn-secondary w-full justify-center">
                            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                            {{ __('home.backoffice') }}
                        </a>
                    @endif
                    <form method="POST" action="{{ route('site.logout') }}">
                        @csrf
                        <button class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm text-stone-500 transition hover:bg-stone-100 hover:text-stone-900">
                            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                            {{ __('home.logout') }}
                        </button>
                    </form>
                </div>

                <x-site.locale-switch class="hidden md:block" />
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <x-site.ticker :ticker="$ticker" />

            <main class="flex-1 px-5 py-8 md:px-10 md:py-10">
                <header class="mb-6 flex flex-wrap items-end justify-between gap-3 md:mb-8">
                    <div>
                        @if ($session)
                            <p class="mb-1.5 text-xs font-semibold tracking-[0.2em] text-brand-700 uppercase">{{ $session }}</p>
                        @endif
                        <h1 class="font-display text-3xl leading-tight tracking-tight text-stone-900 md:text-4xl">{{ __('member.nav.current_bids') }}</h1>
                    </div>
                    @if ($products)
                        <p class="text-sm text-stone-500">{{ trans_choice('member.lots', count($products), ['n' => count($products)]) }}</p>
                    @endif
                </header>

                {{-- Photo 1 et résumé de chaque lot. --}}
                @if ($products)
                    <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:gap-4 xl:grid-cols-4">
                        {{-- $bubble : rang parmi les cartes à cactus (hors SOLD), pour le petit mot de la bulle. --}}
                        @php($bubble = 0)
                        @foreach ($products as $product)
                            <li class="flex"><x-site.product-card :product="$product" :index="$bubble" class="w-full" /></li>
                            @php($bubble += ($product['etat'] ?? null) === 'SOLD' ? 0 : 1)
                        @endforeach
                    </ul>
                @else
                    <div class="flex flex-col items-center gap-3 rounded-2xl border border-dashed border-stone-300 bg-white/60 px-6 py-16 text-center">
                        <svg viewBox="0 0 24 24" class="size-10 text-stone-300" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 21c-1.6-3.6.6-7 5.5-7s7.1 3.4 5.5 7Z"/><path d="M12 14V8"/><path d="M12 10.5c-2-.6-3.4-2.3-3.6-4.5"/><path d="M12 9c1.8-.4 3.2-1.8 3.8-4"/></svg>
                        <p class="text-sm text-stone-500">{{ __('member.empty') }}</p>
                    </div>
                @endif
            </main>

            <x-site.footer>
                {{-- Envoyer un message (demande de membre régulier, inscription…) : liste des membres premium. --}}
                <section aria-labelledby="premium-list">
                    <h2 id="premium-list" class="flex items-center gap-2 text-sm font-medium text-stone-900">
                        <svg viewBox="0 0 24 24" class="size-4 text-brand-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        {{ __('member.send_message') }}
                    </h2>
                    <p class="mt-1 text-sm text-stone-500">{{ __('member.send_message_hint') }}</p>
                    @if ($premiums->isEmpty())
                        <p class="mt-3 text-sm text-stone-400">{{ __('member.no_premium') }}</p>
                    @else
                        <ul class="mt-3 flex flex-wrap gap-2">
                            @foreach ($premiums as $premium)
                                <li>
                                    {{-- TODO : ouvrir l'écran « Envoyer un message » avec ce destinataire. --}}
                                    <a href="#" data-recipient="{{ $premium['id'] }}"
                                       class="flex items-center gap-1.5 rounded-full border border-stone-200 bg-white py-1 pr-3 pl-1.5 text-sm text-stone-700 transition hover:border-brand-600 hover:text-brand-900">
                                        <x-site.grade code="P" />
                                        {{ $premium['identifiant'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </x-site.footer>

            {{-- Mobile : back office, langue (la colonne de gauche est réduite). --}}
            <div class="space-y-3 border-t border-stone-200 bg-white px-5 py-5 md:hidden">
                @if ($user['is_admin'])
                    <a href="{{ route('bo.dashboard') }}" class="btn-secondary w-full justify-center">{{ __('home.backoffice') }}</a>
                @endif
                <x-site.locale-switch />
            </div>
        </div>
    </div>

    <x-site.countdown />
</body>
</html>
