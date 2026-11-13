@props(['code'])
@php
    $code = $code instanceof \BackedEnum ? $code->value : $code;
    $class = match ($code) {
        'A' => 'bg-stone-900 text-white',
        'P' => 'bg-brand-600 text-white',
        'M' => 'bg-brand-100 text-brand-900',
        default => 'bg-stone-100 text-stone-600',
    };
@endphp
<span {{ $attributes->merge(['class' => "inline-flex size-6 items-center justify-center rounded-md text-xs font-bold $class"]) }}>{{ $code }}</span>
