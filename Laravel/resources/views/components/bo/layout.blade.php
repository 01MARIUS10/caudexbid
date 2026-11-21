@props(['title' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Back office' }} · caudexbid</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-800 antialiased">
@php
    // Menu : entrée simple ['label', route, motif actif, icône] ou groupe ['label', icône, 'items' => [...]].
    $icons = [
        'home' => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/>',
        'gavel' => '<path d="m14.5 12.5-8 8a2.12 2.12 0 1 1-3-3l8-8"/><path d="m16 16 6-6"/><path d="m8 8 6-6"/><path d="m9 7 8 8"/><path d="m21 11-8-8"/>',
        'leaf' => '<path d="M6.5 21c-1.6-3.6.6-7 5.5-7s7.1 3.4 5.5 7Z"/><path d="M12 14V8"/><path d="M12 10.5c-2-.6-3.4-2.3-3.6-4.5"/><path d="M12 9c1.8-.4 3.2-1.8 3.8-4"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7"/><path d="M18 14.5a6.5 6.5 0 0 1 3.5 5.5"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'doc' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/>',
    ];
    $nav = [
        ['label' => 'Tableau de bord', 'route' => 'bo.dashboard', 'active' => 'bo.dashboard', 'icon' => 'home'],
        ['label' => 'Enchères', 'icon' => 'gavel', 'items' => [
            ['label' => 'Sessions des enchères', 'route' => 'bo.sessions.index', 'active' => 'bo.sessions.*'],
            ['label' => 'Historique des enchères', 'route' => 'bo.bids.index', 'active' => 'bo.bids.*'],
        ]],
        ['label' => 'Lots & plantes', 'icon' => 'leaf', 'items' => [
            ['label' => 'Lots en vente', 'route' => 'bo.products.index', 'active' => 'bo.products.*'],
            ['label' => 'Propriétaires des plantes', 'route' => 'bo.owners.index', 'active' => 'bo.owners.*'],
        ]],
        ['label' => 'Membres', 'icon' => 'users', 'items' => [
            ['label' => 'Liste des membres', 'route' => 'bo.members.index', 'active' => 'bo.members.*'],
            ['label' => 'Barème des grades', 'route' => 'bo.grades.index', 'active' => 'bo.grades.*'],
        ]],
        ['label' => 'Messagerie', 'route' => 'bo.messages.index', 'active' => 'bo.messages.*', 'icon' => 'mail'],
        ['label' => 'Rapports par session', 'route' => 'bo.documents.index', 'active' => 'bo.documents.*', 'icon' => 'doc'],
    ];
    // Groupe de la page courante : ouvert dans le menu et rappelé au-dessus du titre.
    $section = collect($nav)->first(fn ($e) => isset($e['items']) && collect($e['items'])->contains(fn ($i) => request()->routeIs($i['active'])))['label'] ?? null;
@endphp
<div class="flex min-h-screen">
    <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-stone-200 bg-white md:flex print:hidden">
        <a href="{{ route('bo.dashboard') }}" class="flex items-center gap-2 px-5 py-5">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">cb</span>
            <span class="leading-tight">
                <span class="block text-sm font-semibold text-stone-900">caudexbid</span>
                <span class="block text-xs text-stone-500">Back office</span>
            </span>
        </a>
        <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 pb-3">
            @foreach ($nav as $entry)
                @if (isset($entry['items']))
                    <details class="group/menu" @if ($section === $entry['label']) open @endif>
                        <summary @class([
                            'flex cursor-pointer list-none items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition select-none [&::-webkit-details-marker]:hidden',
                            'font-medium text-stone-900' => $section === $entry['label'],
                            'text-stone-600 hover:bg-stone-50 hover:text-stone-900' => $section !== $entry['label'],
                        ])>
                            <svg viewBox="0 0 24 24" class="size-4 shrink-0 text-stone-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$entry['icon']] !!}</svg>
                            <span class="flex-1">{{ $entry['label'] }}</span>
                            <svg viewBox="0 0 24 24" class="size-3.5 text-stone-400 transition group-open/menu:rotate-90" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                        </summary>
                        <div class="mt-0.5 mb-1 ml-5 space-y-0.5 border-l border-stone-200 pl-2.5">
                            @foreach ($entry['items'] as $item)
                                @php($active = request()->routeIs($item['active']))
                                <a href="{{ route($item['route']) }}" @if ($active) aria-current="page" @endif @class([
                                    'block rounded-lg px-3 py-1.5 text-sm transition',
                                    'bg-brand-50 font-medium text-brand-900' => $active,
                                    'text-stone-600 hover:bg-stone-50 hover:text-stone-900' => ! $active,
                                ])>{{ $item['label'] }}</a>
                            @endforeach
                        </div>
                    </details>
                @else
                    @php($active = request()->routeIs($entry['active']))
                    <a href="{{ route($entry['route']) }}" @if ($active) aria-current="page" @endif @class([
                        'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition',
                        'bg-brand-50 font-medium text-brand-900' => $active,
                        'text-stone-600 hover:bg-stone-50 hover:text-stone-900' => ! $active,
                    ])>
                        <svg viewBox="0 0 24 24" @class(['size-4 shrink-0', 'text-brand-700' => $active, 'text-stone-400' => ! $active]) fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$entry['icon']] !!}</svg>
                        {{ $entry['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
        <form method="POST" action="{{ route('bo.auctions.close') }}" class="px-3 pb-2">
            @csrf
            <button class="w-full rounded-lg border border-dashed border-stone-300 px-3 py-2 text-xs text-stone-500 hover:border-brand-600 hover:text-brand-700">
                Lancer la clôture des enchères
            </button>
        </form>
        <div class="border-t border-stone-100 px-5 py-4 text-xs text-stone-500">
            Heure KST : {{ now()->tz(config('caudexbid.display_timezone'))->format('d/m H:i') }}
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex items-center justify-between gap-4 border-b border-stone-200 bg-white px-6 py-3 print:hidden">
            <div class="min-w-0">
                @if ($section)
                    <p class="text-xs text-stone-400">{{ $section }}</p>
                @endif
                <h1 class="truncate text-lg font-semibold text-stone-900">{{ $title ?? '' }}</h1>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="flex items-center gap-2 text-stone-600">
                    {{ $authUser['identifiant'] }} <x-bo.grade :code="$authUser['grade_code']" />
                </span>
                <a href="{{ route('site.home') }}" class="text-stone-500 hover:text-stone-900">Voir le site</a>
                <form method="POST" action="{{ route('site.logout') }}">
                    @csrf
                    <button class="text-stone-500 hover:text-stone-900">Déconnexion</button>
                </form>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-1 space-y-5 p-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 print:hidden">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 print:hidden">
                    @if ($errors->has('business'))
                        {{ $errors->first('business') }}
                    @else
                        Le formulaire contient des erreurs : {{ implode(' ', $errors->all()) }}
                    @endif
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
