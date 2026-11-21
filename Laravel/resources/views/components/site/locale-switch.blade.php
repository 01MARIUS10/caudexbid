<div {{ $attributes->merge(['class' => 'w-full']) }}>
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
