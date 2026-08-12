<button {{ $attributes->merge(['type' => 'button', 'class' => 'ui-btn ui-btn-outline text-xs uppercase tracking-widest']) }}>
    {{ $slot }}
</button>
