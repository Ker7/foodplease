<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Recipe Manager' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div id="app">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">
                                Recipe Manager
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('recipes.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('recipes.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                                Recipes
                            </a>
                            <a href="{{ route('inventory.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('inventory.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                                Inventory
                            </a>
                            <a href="{{ route('meal-plans.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('meal-plans.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium">
                                Meal Plans
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- HTMX Indicators -->
    <div id="loading-indicator" class="htmx-indicator fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded shadow-lg">
        Loading...
    </div>
</body>
</html>