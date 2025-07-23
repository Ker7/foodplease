@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Recipes</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all your recipes including their ingredients and cooking details.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button type="button" 
                    hx-get="{{ route('recipes.create') }}" 
                    hx-target="#recipe-modal" 
                    hx-indicator="#loading-indicator"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                Add Recipe
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mt-6" x-data="{ search: '', category: 'all' }">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       x-model="search"
                       placeholder="Search recipes..."
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <select x-model="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="all">All Categories</option>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                    <option value="dessert">Dessert</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Recipe Grid -->
    <div id="recipe-list" class="mt-8">
        @include('recipes.partials.list', ['recipes' => $recipes])
    </div>
</div>

<!-- Modal Container -->
<div id="recipe-modal"></div>
@endsection