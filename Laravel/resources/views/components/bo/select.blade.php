@props(['name', 'label', 'options' => [], 'value' => null, 'placeholder' => null])
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->except('class')->merge(['class' => 'input']) }}>
        @if ($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $key => $text)
            <option value="{{ $key }}" @selected((string) old($name, $value) === (string) $key)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
