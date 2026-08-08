<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'CRM') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="darkMode ? 'dark' : ''" @keydown.window="if ((event.metaKey || event.ctrlKey) && event.key === 'k') { event.preventDefault(); $dispatch('toggle-search'); }">
    <div class="flex h-full" x-cloak>

        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-white border-r border-gray-200 z-30">
            {{-- Logo --}}
            <div class="flex items-center h-16 flex-shrink-0 px-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-900">CRM</span>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                @include('layouts.partials.sidebar-nav', ['mobile' => false])
            </nav>
        </aside>

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

        {{-- Mobile Sidebar --}}
        <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl lg:hidden" x-cloak>
            <div class="flex items-center h-16 flex-shrink-0 px-4 border-b border-gray-200 justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-900">CRM</span>
                </div>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                @include('layouts.partials.sidebar-nav', ['mobile' => true])
            </nav>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 lg:pl-64 flex flex-col min-h-full">
            {{-- Mobile Header --}}
            <header class="sticky top-0 z-20 flex items-center h-16 px-4 pt-2 bg-white border-b border-gray-200 lg:hidden">
                <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <div class="flex items-center gap-2 ml-3">
                    <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-xs"></i>
                    </div>
                    <span class="text-base font-bold text-gray-900">CRM</span>
                </div>
            </header>

            {{-- Top Bar --}}
            <div class="hidden lg:flex items-center justify-between h-16 px-6 pt-2 bg-white border-b border-gray-200" style="padding: 1rem;">
                <button @click="$dispatch('toggle-search')" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-search"></i>
                    <span>Search...</span>
                    <kbd class="ml-4 px-1.5 py-0.5 text-xs text-gray-400 bg-white border border-gray-200 rounded">Ctrl+K</kbd>
                </button>
                <div class="flex items-center gap-4">
                    <button @click="darkMode = !darkMode" class="text-gray-400 hover:text-gray-600">
                        <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                    </button>
                    <a href="{{ route('notifications.index') }}" class="relative text-gray-400 hover:text-gray-600">
                        <i class="fas fa-bell text-lg"></i>
                    </a>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-sm"></i>
                        </div>
                        <div class="hidden sm:block">
                            <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</span>
                            <span class="block text-xs text-gray-400 capitalize">{{ auth()->user()->role ?? 'user' }}</span>
                        </div>
                        <a href="{{ route('logout') }}" title="Sign out" class="ml-2 p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Page Content --}}
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Global Search Modal --}}
    <div x-data="{ searchOpen: false, query: '' }" @toggle-search.window="searchOpen = !searchOpen" x-show="searchOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 p-4 sm:p-6 md:p-20" x-cloak>
        <div class="fixed inset-0 bg-gray-900/50" @click="searchOpen = false"></div>
        <div class="relative mx-auto max-w-xl bg-white rounded-xl shadow-2xl overflow-hidden" x-show="searchOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" @click.away="searchOpen = false">
            <div class="flex items-center border-b border-gray-200">
                <i class="fas fa-search ml-4 text-gray-400"></i>
                <input x-ref="searchInput" x-model="query" type="text" placeholder="Search customers, leads, invoices..." class="w-full px-4 py-4 text-sm text-gray-900 placeholder-gray-400 bg-transparent border-0 outline-none focus:ring-0">
                <button @click="searchOpen = false" class="mr-3 text-gray-400 hover:text-gray-600">
                    <kbd class="px-2 py-1 text-xs text-gray-400 bg-gray-100 border border-gray-200 rounded">ESC</kbd>
                </button>
            </div>
            <div class="max-h-80 overflow-y-auto p-2">
                <p class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase">Quick Actions</p>
                @can('permission', 'customers.create')
                <a href="{{ route('customers.create') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-plus w-5 text-center text-gray-400"></i> New Customer
                </a>
                @endcan
                @can('permission', 'leads.create')
                <a href="{{ route('leads.create') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-plus w-5 text-center text-gray-400"></i> New Lead
                </a>
                @endcan
                <div x-show="query.length > 0" class="border-t border-gray-100 mt-2 pt-2">
                    <p class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase">Results</p>
                    <p class="px-3 py-2 text-sm text-gray-500">Type to search...</p>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
