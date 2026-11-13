<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>caudexbid · Enchères de plantes</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-800 antialiased">
    <header class="border-b border-stone-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-4">
            <a href="{{ route('site.home') }}" class="flex items-center gap-2">
                <span class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">cb</span>
                <span class="leading-tight">
                    <span class="block font-semibold text-stone-900">caudexbid</span>
                    <span class="block text-xs text-stone-500">{{ config('caudexbid.exploitant.site') }}</span>
                </span>
            </a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('bo.login') }}" class="btn">Connexion</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-8">
    @php($items = $products->toArray())
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-stone-900">Nos plantes</h1>
            <p class="text-sm text-stone-500">Caudex et succulentes de Madagascar · {{ count($items) }} plantes</p>
        </div>

        @if (empty($items))
            <p class="rounded-xl border border-stone-200 bg-white p-8 text-center text-stone-500">Aucune plante pour le moment.</p>
        @else
            <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($items as $product)
                    <li class="overflow-hidden rounded-xl border border-stone-200 bg-white">
                        @if ($product['photo'])
                            <img src="{{ $product['photo'] }}" alt="{{ $product['nom'] }}" loading="lazy" class="aspect-square w-full object-cover">
                        @else
                            <div class="flex aspect-square items-center justify-center bg-stone-100 text-xs text-stone-400">Photo à venir</div>
                        @endif
                        <div class="p-3">
                            <p class="text-xs text-stone-500">Lot {{ $product['numero_lot'] }}</p>
                            <p class="font-medium text-stone-900">{{ $product['nom'] }}</p>
                            @if ($product['description_1'])
                                <p class="mt-0.5 text-sm text-stone-500">{{ $product['description_1'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </main>

    <footer class="border-t border-stone-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-6 py-5 text-sm text-stone-500">
            <span>Politique de fonctionnement</span>
            {{-- Informations sur l'exploitant : nom du site et e-mail uniquement, pas de téléphone. --}}
            <span>{{ config('caudexbid.exploitant.site') }} · <a class="link" href="mailto:{{ config('caudexbid.exploitant.email') }}">{{ config('caudexbid.exploitant.email') }}</a></span>
        </div>
    </footer>
</body>
</html>
