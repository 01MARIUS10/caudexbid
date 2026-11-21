@props(['code'])
{{-- Grade affiché à côté de l'identifiant : ⓖ général, ⓜ membre régulier, ⓟ premium, ⓐ administrateur. --}}
@php
    $code = $code instanceof \BackedEnum ? $code->value : $code;
    $class = match ($code) {
        'A' => 'border-stone-900 bg-stone-900 text-white',
        'P' => 'border-brand-600 bg-brand-600 text-white',
        'M' => 'border-brand-600 bg-brand-50 text-brand-900',
        default => 'border-stone-300 bg-white text-stone-600',
    };
@endphp
<span title="{{ __('member.grades.'.$code) }}"
      {{ $attributes->merge(['class' => "inline-flex size-5 shrink-0 items-center justify-center rounded-full border text-[11px] leading-none font-bold lowercase $class"]) }}>{{ strtolower($code) }}<span class="sr-only"> ({{ __('member.grades.'.$code) }})</span></span>
