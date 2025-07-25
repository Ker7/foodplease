@php
    $groupedInventory = $inventory->groupBy('category');
    $categories = [
        'fridge' => ['name' => 'Fridge', 'icon' => '🧊', 'color' => 'blue'],
        'pantry' => ['name' => 'Pantry', 'icon' => '🍽️', 'color' => 'green'],
        'freezer' => ['name' => 'Freezer', 'icon' => '❄️', 'color' => 'indigo']
    ];
@endphp

@if($inventory->count() > 0)
    @foreach($categories as $categoryKey => $categoryInfo)
        @if($groupedInventory->has($categoryKey))
            <div class="mb-4">
                <div class="bg-{{ $categoryInfo['color'] }}-50 px-4 py-3 border-l-4 border-{{ $categoryInfo['color'] }}-400 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl mr-3">{{ $categoryInfo['icon'] }}</span>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-medium text-gray-900">{{ $categoryInfo['name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $groupedInventory[$categoryKey]->count() }} items</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul role="list" class="divide-y divide-gray-200">
                        @foreach($groupedInventory[$categoryKey] as $item)
                            @include('inventory.partials.item', ['item' => $item])
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            <li class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No inventory items</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first inventory item.</p>
                <div class="mt-6">
                    <button type="button" 
                            hx-get="{{ route('inventory.create') }}" 
                            hx-target="#inventory-modal"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Item
                    </button>
                </div>
            </li>
        </ul>
    </div>
@endif