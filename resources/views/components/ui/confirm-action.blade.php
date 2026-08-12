@props([
    'action',
    'title' => 'Konfirmasi tindakan',
    'message' => 'Apakah Anda yakin ingin melanjutkan?',
    'confirmLabel' => 'Ya, lanjutkan',
    'cancelLabel' => 'Batal',
    'variant' => 'danger',
    'buttonVariant' => 'ghost',
    'size' => 'md',
])

@php
    $modalName = 'confirmation-'.md5($action.$title);
    $variantLabel = [
        'danger' => 'Tindakan Destruktif',
        'warning' => 'Perlu Perhatian',
        'success' => 'Konfirmasi',
        'info' => 'Informasi',
    ][$variant] ?? 'Konfirmasi';
@endphp

<x-ui.button
    type="button"
    :variant="$buttonVariant"
    :size="$size"
    class="{{ $attributes->get('class') }}"
    x-on:click.prevent="$dispatch('open-modal', '{{ $modalName }}')"
>
    {{ $slot }}
</x-ui.button>

<x-modal :name="$modalName" max-width="md" focusable>
    <div class="p-6" x-data="{ processing: false }">
        <x-ui.badge :variant="$variant" class="mb-3">{{ $variantLabel }}</x-ui.badge>
        <h2 class="text-lg font-bold text-content">{{ $title }}</h2>
        <p class="mt-2 text-sm leading-6 text-muted">{{ $message }}</p>

        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <x-ui.button variant="outline" x-on:click="$dispatch('close-modal', '{{ $modalName }}')" x-bind:disabled="processing">
                {{ $cancelLabel }}
            </x-ui.button>
            <x-ui.button
                :variant="$variant"
                x-bind:disabled="processing"
                x-on:click="processing = true; Promise.resolve($wire.{{ $action }}).then(() => $dispatch('close-modal', '{{ $modalName }}')).finally(() => processing = false)"
            >
                <span x-show="processing" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent" aria-hidden="true"></span>
                {{ $confirmLabel }}
            </x-ui.button>
        </div>
    </div>
</x-modal>
