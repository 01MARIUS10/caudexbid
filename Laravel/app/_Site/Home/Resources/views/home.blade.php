<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>caudexbid · {{ __('home.title') }}</title>
    <meta name="description" content="{{ __('home.tagline') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&display=swap">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-800 antialiased">
    <div class="mx-auto flex min-h-screen max-w-[1440px] flex-col md:flex-row">

        {{-- Colonne de gauche : site, connexion, langue. --}}
        <aside class="z-10 shrink-0 border-b border-stone-200 bg-white md:sticky md:top-0 md:h-screen md:w-64 md:border-r md:border-b-0">
            <div class="flex h-full flex-wrap items-center justify-between gap-4 px-5 py-4 md:flex-col md:flex-nowrap md:items-stretch md:justify-start md:gap-10 md:px-7 md:py-9">
                <x-site.brand />

                <nav class="flex items-center gap-2 md:flex-col md:items-stretch" aria-label="{{ __('home.account') }}">
                    <a href="{{ route('site.login') }}" class="btn justify-center md:py-2.5">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/></svg>
                        {{ __('home.login') }}
                    </a>
                </nav>

                <p class="hidden text-sm leading-relaxed text-stone-500 md:block">{{ __('home.tagline') }}</p>

                <x-site.locale-switch class="md:mt-auto" />
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <x-site.ticker :ticker="$ticker" />

            <main class="flex-1 px-5 py-8 md:px-10 md:py-12">
                <header class="mb-8 space-y-4 md:mb-10">
                    <div>
                        <p class="mb-2 text-xs font-semibold tracking-[0.2em] text-brand-700 uppercase">{{ __('home.eyebrow') }}</p>
                        <h1 class="font-display text-3xl leading-tight tracking-tight text-stone-900 md:text-5xl">{{ __('home.heading') }}</h1>
                    </div>
                    <p class="max-w-xl text-sm leading-relaxed text-stone-500">{{ __('home.intro') }}</p>
                </header>

                {{-- 20 photos maximum, sans aucune information sur les plantes. --}}
                @if ($photos->isEmpty())
                    <div class="relative">
                        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:gap-4 lg:grid-cols-4" aria-hidden="true">
                            @for ($i = 0; $i < 8; $i++)
                                <li class="flex aspect-square items-center justify-center rounded-2xl border border-dashed border-stone-300 bg-white/60">
                                    <svg viewBox="0 0 24 24" class="size-8 text-stone-300" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 21c-1.6-3.6.6-7 5.5-7s7.1 3.4 5.5 7Z"/><path d="M12 14V8"/><path d="M12 10.5c-2-.6-3.4-2.3-3.6-4.5"/><path d="M12 9c1.8-.4 3.2-1.8 3.8-4"/></svg>
                                </li>
                            @endfor
                        </ul>
                        <p class="absolute inset-x-0 top-1/2 mx-auto w-fit -translate-y-1/2 rounded-full bg-white px-5 py-2.5 text-sm text-stone-600 shadow-md ring-1 ring-stone-200">{{ __('home.no_photos') }}</p>
                    </div>
                @else
                    <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:gap-4 lg:grid-cols-4">
                        @foreach ($photos as $url)
                            <li class="group relative overflow-hidden rounded-2xl bg-stone-200 shadow-sm ring-1 ring-stone-900/5">
                                <img src="{{ $url }}" alt="" loading="lazy" decoding="async"
                                     class="aspect-square size-full object-cover transition duration-700 ease-out group-hover:scale-[1.04]">
                                <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-stone-900/15 to-transparent opacity-0 transition duration-500 group-hover:opacity-100"></span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </main>

            <x-site.footer />
        </div>
    </div>

    <x-site.countdown />
</body>
</html>
