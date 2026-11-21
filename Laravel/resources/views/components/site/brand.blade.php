<a href="{{ route('site.home') }}" {{ $attributes->merge(['class' => 'group flex items-center gap-3']) }}>
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
