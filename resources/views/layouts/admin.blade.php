<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin · {{ $storeSettings->store_name ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-canvas font-sans antialiased">
        <div class="flex min-h-screen flex-col lg:flex-row">

            {{-- Sidebar --}}
            <livewire:layout.sidebar />

            {{-- Main Content --}}
            <div class="flex flex-1 flex-col">

                {{-- Page Content --}}
                <main class="min-w-0 flex-1 p-4 sm:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <x-ui.toast />
        @stack('scripts')
    </body>
</html>
