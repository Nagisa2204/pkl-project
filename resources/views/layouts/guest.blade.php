<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $storeSettings->store_name ?? config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-content antialiased">
        <div class="flex min-h-screen flex-col items-center bg-canvas pt-6 sm:justify-center sm:pt-0">
            <div>
                <a href="/" wire:navigate>
                    <x-application-logo class="h-20 w-20 fill-current text-muted" />
                </a>
            </div>
            <div class="ui-card mt-6 w-full overflow-hidden px-6 py-4 sm:max-w-md">
                {{ $slot }}
            </div>
        </div>
        <x-ui.toast />
        @stack('scripts')
    </body>
</html>
