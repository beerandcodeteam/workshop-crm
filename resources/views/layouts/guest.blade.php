<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Workshop CRM') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-bg-light font-sans antialiased">
    <div class="flex min-h-screen">
        {{-- Left: Form area --}}
        <div class="flex w-full flex-col justify-center px-8 py-12 lg:w-1/2 lg:px-20">
            <div class="mx-auto w-full max-w-md">
                {{ $slot }}
            </div>
        </div>

        {{-- Right: Illustration area --}}
        <div class="hidden bg-primary lg:flex lg:w-1/2 lg:items-center lg:justify-center">
            <div class="p-12 text-center text-white">
                <svg class="mx-auto mb-6 size-24 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="text-2xl font-bold">Workshop CRM</h2>
                <p class="mt-2 text-sm opacity-80">Gerencie suas vendas de forma simples e eficiente.</p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
