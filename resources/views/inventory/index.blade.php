@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Inventory</h1>
            <p class="mt-2 text-sm text-gray-700">Track your food inventory with expiration dates and stock levels.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button type="button" 
                    hx-get="{{ route('inventory.create') }}" 
                    hx-target="#inventory-modal" 
                    hx-indicator="#loading-indicator"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                Add Item
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="mt-6" x-data="{ category: 'all', showExpiring: false, showLowStock: false }">
        <div class="flex flex-wrap gap-4">
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Location</label>
                <select x-model="category" 
                        hx-get="{{ route('inventory.index') }}" 
                        hx-target="#inventory-list"
                        hx-trigger="change"
                        hx-include="[name='expiring_soon'], [name='low_stock']"
                        name="category"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Locations</option>
                    <option value="fridge">Fridge</option>
                    <option value="pantry">Pantry</option>
                    <option value="freezer">Freezer</option>
                </select>
            </div>
            <div class="flex items-end space-x-4">
                <div class="flex items-center">
                    <input type="checkbox" 
                           x-model="showExpiring"
                           hx-get="{{ route('inventory.index') }}" 
                           hx-target="#inventory-list"
                           hx-trigger="change"
                           hx-include="[name='category'], [name='low_stock']"
                           name="expiring_soon"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Expiring Soon</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           x-model="showLowStock"
                           hx-get="{{ route('inventory.index') }}" 
                           hx-target="#inventory-list"
                           hx-trigger="change"
                           hx-include="[name='category'], [name='expiring_soon']"
                           name="low_stock"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Low Stock</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory List -->
    <div id="inventory-list" class="mt-8">
        @include('inventory.partials.list', ['inventory' => $inventory])
    </div>
</div>

<!-- Modal Container -->
<div id="inventory-modal"></div>
@endsection