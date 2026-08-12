<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Js;

test('searchable select uses domain ids instead of numeric array indexes', function () {
    $html = Blade::render(
        '<x-ui.searchable-select wire:model="selectedProvinceId" :options="$options" />',
        [
            'options' => [
                ['id' => 11, 'name' => 'ACEH'],
                ['id' => 12, 'name' => 'SUMATERA UTARA'],
            ],
        ],
    );

    expect($html)->toContain((string) Js::from([
        ['value' => '11', 'label' => 'ACEH'],
        ['value' => '12', 'label' => 'SUMATERA UTARA'],
    ]));
});

test('searchable select keeps explicit values and associative options compatible', function () {
    $arrayOptions = Blade::render(
        '<x-ui.searchable-select wire:model="status" :options="$options" />',
        ['options' => [['value' => 'paid', 'label' => 'Dibayar']]],
    );

    $associativeOptions = Blade::render(
        '<x-ui.searchable-select wire:model="category" :options="$options" />',
        ['options' => ['physical' => 'Produk Fisik']],
    );

    expect($arrayOptions)->toContain((string) Js::from([
        ['value' => 'paid', 'label' => 'Dibayar'],
    ]))->and($associativeOptions)->toContain((string) Js::from([
        ['value' => 'physical', 'label' => 'Produk Fisik'],
    ]));
});
