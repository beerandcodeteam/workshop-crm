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
<body class="min-h-screen bg-bg-light font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        {{-- Mobile overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-primary-dark/50 lg:hidden"
            @click="sidebarOpen = false"
            x-cloak
        ></div>

        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-bg-white shadow-lg transition-transform duration-200 lg:static lg:translate-x-0"
        >
            {{-- Company logo/avatar --}}
            <div class="flex items-center gap-3 border-b border-outline px-4 py-5">
                <div class="flex size-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">
                    {{ substr(auth()->user()->tenant->name ?? 'W', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-primary-dark">{{ auth()->user()->tenant->name ?? 'Workshop CRM' }}</p>
                    <p class="text-xs text-primary-grey">{{ auth()->user()->name ?? '' }}</p>
                </div>
            </div>

            {{-- Welcome --}}
            <div class="px-4 py-4">
                <p class="text-xs text-primary-grey">Bem-vindo,</p>
                <p class="text-sm font-semibold text-primary-dark">{{ auth()->user()->name ?? '' }}</p>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 space-y-1 px-3">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('dashboard.*') ? 'bg-primary/10 text-primary' : 'text-primary-grey hover:bg-bg-light hover:text-primary-dark' }}" wire:navigate>
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('kanban.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('kanban.*') ? 'bg-primary/10 text-primary' : 'text-primary-grey hover:bg-bg-light hover:text-primary-dark' }}" wire:navigate>
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    Kanban
                </a>
                <a href="{{ route('team.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('team.*') ? 'bg-primary/10 text-primary' : 'text-primary-grey hover:bg-bg-light hover:text-primary-dark' }}" wire:navigate>
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Equipe
                </a>
            </nav>

            {{-- Bottom section --}}
            <div class="border-t border-outline p-3">
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-primary-grey transition-colors hover:bg-bg-light hover:text-primary-dark {{ request()->routeIs('settings.*') ? 'bg-primary/10 text-primary' : '' }}" wire:navigate>
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Configurações
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-primary-grey transition-colors hover:bg-bg-light hover:text-secondary-red">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sair
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex flex-1 flex-col">
            {{-- Top bar --}}
            <header class="flex items-center justify-between border-b border-outline bg-bg-white px-6 py-4">
                <div class="flex items-center gap-4">
                    {{-- Hamburger menu (mobile) --}}
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="text-primary-grey hover:text-primary-dark lg:hidden"
                    >
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h1 class="text-lg font-semibold text-primary-dark">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-sm text-primary-grey">
                        {{ auth()->user()->name ?? '' }}
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            <div class="px-6 pt-4">
                @session('success')
                    <div class="mb-4 rounded-lg bg-secondary-green/10 px-4 py-3 text-sm text-secondary-green">
                        {{ $value }}
                    </div>
                @endsession

                @session('error')
                    <div class="mb-4 rounded-lg bg-secondary-red/10 px-4 py-3 text-sm text-secondary-red">
                        {{ $value }}
                    </div>
                @endsession
            </div>

            {{-- Page content --}}
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
