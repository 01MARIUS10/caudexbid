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
    $nav = [
        ['Tableau de bord', 'bo.dashboard', 'bo.dashboard'],
        ['Membres', 'bo.members.index', 'bo.members.*'],
        ['Grades', 'bo.grades.index', 'bo.grades.*'],
        ['Messages', 'bo.messages.index', 'bo.messages.*'],
        null,
        ['Sessions', 'bo.sessions.index', 'bo.sessions.*'],
        ['Produits', 'bo.products.index', 'bo.products.*'],
        ['Enchères', 'bo.bids.index', 'bo.bids.*'],
        ['Propriétaires', 'bo.owners.index', 'bo.owners.*'],
        ['Documents', 'bo.documents.index', 'bo.documents.*'],
    ];
@endphp
<div class="flex min-h-screen">
    <aside class="sticky top-0 hidden h-screen w-56 shrink-0 flex-col border-r border-stone-200 bg-white md:flex print:hidden">
        <a href="{{ route('bo.dashboard') }}" class="flex items-center gap-2 px-5 py-5">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">cb</span>
            <span class="leading-tight">
                <span class="block text-sm font-semibold text-stone-900">caudexbid</span>
                <span class="block text-xs text-stone-500">Back office</span>
            </span>
        </a>
        <nav class="flex-1 space-y-0.5 px-3">
            @foreach ($nav as $item)
                @if ($item === null)
                    <div class="my-3 border-t border-stone-100"></div>
                @else
                    @php [$label, $route, $pattern] = $item; $active = request()->routeIs($pattern); @endphp
                    <a href="{{ route($route) }}" @class([
                        'block rounded-lg px-3 py-2 text-sm transition',
                        'bg-brand-50 font-medium text-brand-900' => $active,
                        'text-stone-600 hover:bg-stone-50 hover:text-stone-900' => ! $active,
                    ])>{{ $label }}</a>
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
            <h1 class="text-lg font-semibold text-stone-900">{{ $title ?? '' }}</h1>
            <div class="flex items-center gap-3 text-sm">
                <span class="flex items-center gap-2 text-stone-600">
                    {{ $authUser['identifiant'] }} <x-bo.grade :code="$authUser['grade_code']" />
                </span>
                <form method="POST" action="{{ route('bo.logout') }}">
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
