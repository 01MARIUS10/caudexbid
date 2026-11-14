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
                <a href="{{ route('site.home') }}" class="group flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-brand-900 text-brand-100 shadow-sm transition group-hover:bg-brand-700">
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6.5 21c-1.6-3.6.6-7 5.5-7s7.1 3.4 5.5 7Z"/>
                            <path d="M12 14V8"/>
                            <path d="M12 10.5c-2-.6-3.4-2.3-3.6-4.5"/>
                            <path d="M12 9c1.8-.4 3.2-1.8 3.8-4"/>
                        </svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block font-display text-xl tracking-tight text-stone-900">caudexbid</span>
                        <span class="block text-xs text-stone-500">{{ config('caudexbid.exploitant.site') }}</span>
                    </span>
                </a>

                <nav class="flex items-center gap-2 md:flex-col md:items-stretch" aria-label="{{ __('home.account') }}">
                    <a href="{{ route('bo.login') }}" class="btn justify-center md:py-2.5">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/></svg>
                        {{ __('home.login') }}
                    </a>
                </nav>

                <p class="hidden text-sm leading-relaxed text-stone-500 md:block">{{ __('home.tagline') }}</p>

                <div class="w-full md:mt-auto">
                    <p class="mb-2 hidden items-center gap-1.5 text-xs font-medium tracking-wide text-stone-500 uppercase md:flex">
                        <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10Z"/></svg>
                        {{ __('home.language') }}
                    </p>
                    <div class="flex flex-wrap gap-1 rounded-xl bg-stone-100 p-1">
                        @foreach (\App\Middleware\SetLocale::LOCALES as $code => $label)
                            <a href="{{ route('site.locale', $code) }}" lang="{{ $code }}" hreflang="{{ $code }}"
                               @if (app()->getLocale() === $code) aria-current="true" @endif
                               @class([
                                   'flex-auto rounded-lg px-2.5 py-1.5 text-center text-sm transition',
                                   'bg-white font-semibold text-brand-900 shadow-sm' => app()->getLocale() === $code,
                                   'text-stone-600 hover:bg-white/60 hover:text-stone-900' => app()->getLocale() !== $code,
                               ])>{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Bandeau défilant : date de clôture / temps restant / date de la prochaine enchère. --}}
            <div class="flex items-stretch bg-brand-900 text-sm text-brand-50" role="status">
                <span class="z-10 flex shrink-0 items-center gap-2 bg-brand-900 border-r border-white/10 px-5 py-3 text-xs font-semibold tracking-widest uppercase">
                    <span class="relative flex size-2">
                        @if ($ticker['restant'])
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-300 opacity-75"></span>
                        @endif
                        <span @class(['relative inline-flex size-2 rounded-full', 'bg-emerald-300' => $ticker['restant'], 'bg-stone-400' => ! $ticker['restant']])></span>
                    </span>
                    {{ $ticker['restant'] ? __('home.live') : __('home.auction') }}
                </span>
                <div class="marquee flex-1 py-3">
                    <div class="marquee-track">
                        @for ($i = 0; $i < 2; $i++)
                            <span class="flex shrink-0 items-center gap-10 pr-10" @if ($i) aria-hidden="true" @endif>
                                @if ($ticker['cloture'] || $ticker['prochaine'])
                                    @if ($ticker['cloture'])
                                        <span class="flex items-center gap-2">
                                            <svg viewBox="0 0 24 24" class="size-4 text-brand-100/70" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 22V4"/><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1"/></svg>
                                            <span class="text-brand-100/80">{{ __('home.closing') }}</span>
                                            <strong class="font-semibold tabular-nums">{{ $ticker['cloture'] }} KST</strong>
                                        </span>
                                    @endif
                                    @if ($ticker['restant'])
                                        <span class="text-brand-100/40" aria-hidden="true">✦</span>
                                        <span class="flex items-center gap-2">
                                            <svg viewBox="0 0 24 24" class="size-4 text-brand-100/70" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                            <span class="text-brand-100/80">{{ __('home.remaining') }}</span>
                                            <strong class="font-semibold tabular-nums" data-countdown="{{ $ticker['cloture_iso'] }}">{{ $ticker['restant'] }}</strong>
                                        </span>
                                    @endif
                                    @if ($ticker['prochaine'])
                                        <span class="text-brand-100/40" aria-hidden="true">✦</span>
                                        <span class="flex items-center gap-2">
                                            <svg viewBox="0 0 24 24" class="size-4 text-brand-100/70" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                                            <span class="text-brand-100/80">{{ __('home.next') }}</span>
                                            <strong class="font-semibold tabular-nums">{{ $ticker['prochaine'] }} KST</strong>
                                        </span>
                                    @endif
                                    <span class="text-brand-100/40" aria-hidden="true">✦</span>
                                @else
                                    <span class="text-brand-100/80">{{ __('home.no_ticker') }}</span>
                                    <span class="text-brand-100/40" aria-hidden="true">✦</span>
                                @endif
                            </span>
                        @endfor
                    </div>
                </div>
            </div>

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

            <footer class="border-t border-stone-200 bg-white">
                <div class="flex flex-col gap-6 px-5 py-8 text-sm md:flex-row md:items-start md:justify-between md:px-10">
                    <div class="space-y-1.5">
                        <p class="font-medium text-stone-900">{{ __('home.policy') }}</p>
                        <p class="text-stone-500">© {{ now()->year }} {{ config('caudexbid.exploitant.site') }}</p>
                    </div>
                    {{-- Informations sur l'administrateur : nom du site et e-mail uniquement, pas de téléphone. --}}
                    <div class="space-y-1.5 md:text-right">
                        <p class="font-medium text-stone-900">{{ __('home.operator') }}</p>
                        <p class="text-stone-500">
                            {{ config('caudexbid.exploitant.site') }}
                            <span class="mx-1.5 text-stone-300">·</span>
                            <a class="link" href="mailto:{{ config('caudexbid.exploitant.email') }}">{{ config('caudexbid.exploitant.email') }}</a>
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @if ($ticker['restant'])
        {{-- Compte à rebours en direct (le texte serveur reste affiché sans JavaScript). --}}
        <script>
            (() => {
                const els = document.querySelectorAll('[data-countdown]');
                const end = Date.parse(els[0].dataset.countdown);
                const days = new Intl.NumberFormat(document.documentElement.lang, { style: 'unit', unit: 'day', unitDisplay: 'short' });
                const pad = (n) => String(n).padStart(2, '0');
                const tick = () => {
                    const s = Math.max(0, Math.floor((end - Date.now()) / 1000));
                    const d = Math.floor(s / 86400);
                    els.forEach((el) => (el.textContent = (d ? days.format(d) + ' ' : '') + `${pad(Math.floor(s / 3600) % 24)}:${pad(Math.floor(s / 60) % 60)}:${pad(s % 60)}`));
                    if (s === 0) clearInterval(timer);
                };
                const timer = setInterval(tick, 1000);
                tick();
            })();
        </script>
    @endif
</body>
</html>
