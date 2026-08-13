@props([
    'options' => [],
    'placeholder' => 'Pilih opsi',
    'searchPlaceholder' => 'Cari...',
    'emptyText' => 'Tidak ada opsi ditemukan.',
    'multiple' => false,
    'clearable' => true,
    'disabled' => false,
    'loadingTarget' => null,
    'label' => null,
    'instanceKey' => null,
])

@php
    $model = $attributes->wire('model')->value();
    $normalizedOptions = collect($options)->map(function ($option, $key) {
        if (is_array($option)) {
            return [
                'value' => (string) ($option['id'] ?? $option['value'] ?? $key),
                'label' => (string) ($option['label'] ?? $option['name'] ?? $option['value'] ?? $key),
            ];
        }

        if (is_object($option)) {
            return [
                'value' => (string) ($option->id ?? $option->value ?? $key),
                'label' => (string) ($option->name ?? $option->label ?? $option->value ?? $key),
            ];
        }

        return ['value' => (string) $key, 'label' => (string) $option];
    })->values()->all();
    $hasError = $model && $errors->has($model);
@endphp

<div class="ui-search-select" @if($instanceKey) wire:key="{{ $instanceKey }}" @endif x-data="window.createSearchableSelect({
    value: $wire.entangle(@js($model)).live,
    options: @js($normalizedOptions),
    multiple: @js($multiple),
    placeholder: @js($placeholder),
})" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false">
    @if($label)
        <label class="ui-field-label">{{ $label }}</label>
    @endif

    <button
        type="button"
        class="ui-search-select-trigger"
        x-on:click="open = !open"
        :aria-expanded="open"
        aria-haspopup="listbox"
        aria-invalid="{{ $hasError ? 'true' : 'false' }}"
        @disabled($disabled || ! $model)
        @if($loadingTarget) wire:loading.attr="disabled" wire:target="{{ $loadingTarget }}" @endif
    >
        <span class="min-w-0 flex-1 truncate" :class="selectedValues.length ? 'text-content' : 'text-muted'" x-text="label"></span>
        <span class="flex shrink-0 items-center gap-1">
            @if($clearable)
                <span x-cloak x-show="selectedValues.length" role="button" tabindex="0" class="rounded px-1 text-muted hover:bg-subtle hover:text-content" x-on:click.stop="clear()" x-on:keydown.enter.stop.prevent="clear()" aria-label="Hapus pilihan">×</span>
            @endif
            @if($loadingTarget)
                <span wire:loading wire:target="{{ $loadingTarget }}" class="h-4 w-4 animate-spin rounded-full border-2 border-primary border-r-transparent" aria-label="Memuat"></span>
            @endif
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    <div x-cloak x-show="open" x-transition class="ui-search-select-panel">
        <div class="border-b border-default p-2">
            <input x-model="query" type="search" class="ui-field text-sm" placeholder="{{ $searchPlaceholder }}" x-ref="search" x-init="$watch('open', value => value && $nextTick(() => $refs.search.focus()))">
        </div>
        <div class="max-h-64 overflow-y-auto py-1" role="listbox" @if($multiple) aria-multiselectable="true" @endif>
            <template x-for="option in filteredOptions" :key="option.value">
                <button type="button" class="ui-search-select-option" role="option" :aria-selected="isSelected(option.value)" x-on:click="select(option.value)">
                    <span x-text="option.label"></span>
                    <span x-show="isSelected(option.value)" class="font-bold text-primary" aria-hidden="true">✓</span>
                </button>
            </template>
            <p x-show="filteredOptions.length === 0" class="px-3 py-6 text-center text-sm text-muted">{{ $emptyText }}</p>
        </div>
    </div>

    @if($hasError)
        <p class="ui-field-error">{{ $errors->first($model) }}</p>
    @endif
</div>
