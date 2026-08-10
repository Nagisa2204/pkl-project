@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'loading' => false,
    'href' => null,
])

@php
    $variantClass = [
        'primary' => 'ui-btn-primary',
        'secondary' => 'ui-btn-secondary',
        'outline' => 'ui-btn-outline',
        'ghost' => 'ui-btn-ghost',
        'success' => 'ui-btn-success',
        'warning' => 'ui-btn-warning',
        'danger' => 'ui-btn-danger',
    ][$variant] ?? 'ui-btn-primary';

    $sizeClass = [
        'sm' => 'min-h-8 px-3 py-1.5 text-xs',
        'md' => '',
        'lg' => 'min-h-12 px-5 py-3 text-base',
    ][$size] ?? '';

    $classes = trim("ui-btn {$variantClass} {$sizeClass}");
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes)->merge(['disabled' => $loading]) }}>
        @if($loading)
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent" aria-hidden="true"></span>
        @endif
        {{ $slot }}
    </button>
@endif
