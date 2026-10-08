<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Docustream Admin') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-screen flex flex-col m-0 p-0">

    <!-- Top Navigation -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 h-14 flex justify-between items-center">
            <div class="flex items-center gap-8">

                <!-- Logo -->
                @php
                $homeRoute = '/';
                if (auth()->check()) {
                if (auth()->user()->hasRole('super-admin')) {
                $homeRoute = route('admin.dashboard');
                } elseif (auth()->user()->hasRole('manager')) {
                $homeRoute = route('manager.clients.index');
                } elseif (auth()->user()->hasRole('client')) {
                $homeRoute = route('client.portal');
                }
                }
                @endphp

                <a href="{{ $homeRoute }}" class="text-lg font-bold text-slate-900 no-underline flex items-center gap-2">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    Docustream
                </a>

                <!-- Nav Links -->
                <div class="flex gap-2">
                    @if(auth()->user()->hasRole('super-admin'))
                    <a href="{{ route('admin.dashboard') }}" class="text-sm px-3 py-1.5 rounded-md transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'font-semibold text-slate-900 bg-slate-100' : 'font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                        Company Management
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="text-sm px-3 py-1.5 rounded-md transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'font-semibold text-slate-900 bg-slate-100' : 'font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                        Audit Logs
                    </a>
                    @elseif(auth()->user()->hasRole('manager'))
                    <a href="{{ route('manager.clients.index') }}" class="text-sm px-3 py-1.5 rounded-md transition-all duration-200 {{ request()->routeIs('manager.clients.*') ? 'font-semibold text-slate-900 bg-slate-100' : 'font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                        Client Management
                    </a>
                    <a href="{{ route('manager.financial.index') }}" class="text-sm px-3 py-1.5 rounded-md transition-all duration-200 {{ request()->routeIs('manager.financial.*') ? 'font-semibold text-slate-900 bg-slate-100' : 'font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                        Financial Audits
                    </a>
                    @elseif(auth()->user()->hasRole('client'))
                    <a href="{{ route('client.portal') }}" class="text-sm px-3 py-1.5 rounded-md transition-all duration-200 {{ request()->routeIs('client.portal') ? 'font-semibold text-slate-900 bg-slate-100' : 'font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                        Corporate Documents Portal
                    </a>
                    @endif
                </div>
            </div>

            <!-- User Actions -->
            <div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-700 bg-transparent border-none cursor-pointer px-3 py-1.5 transition-colors">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 p-8 w-full max-w-7xl mx-auto">
        {{ $slot }}
    </main>

</body>

</html>