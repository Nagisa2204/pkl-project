@props(['variant' => 'secondary'])

@php
    $class = [
        'primary' => 'ui-badge-primary',
        'secondary' => 'ui-badge-secondary',
        'success' => 'ui-badge-success',
        'warning' => 'ui-badge-warning',
        'danger' => 'ui-badge-danger',
        'info' => 'ui-badge-info',
    ][$variant] ?? 'ui-badge-secondary';
@endphp

<span {{ $attributes->class("ui-badge {$class}") }}>{{ $slot }}</span>
