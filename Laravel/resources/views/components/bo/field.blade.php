@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="3" {{ $attributes->except('class')->merge(['class' => 'input']) }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type === 'password' ? '' : old($name, $value) }}" {{ $attributes->except('class')->merge(['class' => 'input']) }}>
    @endif
    @if ($hint)<p class="mt-1 text-xs text-stone-500">{{ $hint }}</p>@endif
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
