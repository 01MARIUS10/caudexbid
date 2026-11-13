@props(['title' => null, 'subtitle' => null])
<section {{ $attributes->merge(['class' => 'rounded-xl border border-stone-200 bg-white']) }}>
    @if ($title || isset($actions))
        <header class="flex items-center justify-between gap-4 border-b border-stone-100 px-5 py-3">
            <div>
                <h2 class="text-sm font-semibold text-stone-900">{{ $title }}</h2>
                @if ($subtitle)<p class="text-xs text-stone-500">{{ $subtitle }}</p>@endif
            </div>
            {{ $actions ?? '' }}
        </header>
    @endif
    <div class="p-5">{{ $slot }}</div>
</section>
