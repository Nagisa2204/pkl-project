@props([
    'padding' => true,
])

<section {{ $attributes->class(['ui-card', 'ui-card-body' => $padding]) }}>
    @isset($header)
        <div class="ui-card-header">{{ $header }}</div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="ui-card-footer">{{ $footer }}</div>
    @endisset
</section>
