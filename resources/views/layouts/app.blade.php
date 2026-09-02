<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $storeSettings->store_name ?? config('app.name') }}</title>
        @if($storeSettings?->favicon_path)<link rel="icon" href="{{ Storage::url($storeSettings->favicon_path) }}">@endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col bg-canvas">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="border-b border-default bg-surface shadow-ui">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>
            <footer class="border-t border-default bg-surface">
                <div class="mx-auto max-w-7xl px-4 py-8 text-sm text-muted">
                    <strong class="text-content">{{ $storeSettings->store_name }}</strong>
                    <p class="mt-1">{{ collect([$storeSettings->address, $storeSettings->city, $storeSettings->province])->filter()->implode(', ') }}</p>
                    <p>{{ $storeSettings->email }} {{ $storeSettings->whatsapp ? ' · '.$storeSettings->whatsapp : '' }}</p>
                </div>
            </footer>
        </div>
        <x-ui.toast />
        @stack('scripts')
    </body>
</html>
