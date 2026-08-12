@props(['variant' => 'info'])

@php
    $class = [
        'success' => 'ui-alert-success',
        'warning' => 'ui-alert-warning',
        'danger' => 'ui-alert-danger',
        'info' => 'ui-alert-info',
    ][$variant] ?? 'ui-alert-info';
@endphp

<div role="alert" {{ $attributes->class("ui-alert {$class}") }}>{{ $slot }}</div>
