@props(['label', 'value', 'href' => null, 'hint' => null])
<a @if ($href) href="{{ $href }}" @endif class="block rounded-xl border border-stone-200 bg-white p-4 transition hover:border-brand-600">
    <p class="text-xs font-medium tracking-wide text-stone-500 uppercase">{{ $label }}</p>
    <p class="mt-1 text-2xl font-semibold text-stone-900 tabular-nums">{{ $value }}</p>
    @if ($hint)<p class="mt-0.5 text-xs text-stone-500">{{ $hint }}</p>@endif
</a>
