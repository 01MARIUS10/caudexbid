@props(['ticker'])
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
