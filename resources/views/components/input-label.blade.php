@props(['value'])

<label {{ $attributes->merge(['class' => 'ui-field-label']) }}>
    {{ $value ?? $slot }}
</label>
