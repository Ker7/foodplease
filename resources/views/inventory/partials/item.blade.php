<li class="px-6 py-4">
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 truncate">
                        {{ $item->name }}
                        @if($item->is_expiring_soon)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Expiring Soon
                            </span>
                        @endif
                        @if($item->is_low_stock)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Low Stock
                            </span>
                        @endif
                    </p>
                    <div class="mt-1 flex items-center text-sm text-gray-500 space-x-4">
                        <span class="flex items-center">
                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ ucfirst($item->category) }}
                            @if($item->location)
                                - {{ $item->location }}
                            @endif
                        </span>
                        <span class="flex items-center">
                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            {{ $item->quantity }} {{ $item->unit->slug }}
                        </span>
                        @if($item->expiry_date)
                            <span class="flex items-center">
                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4H8zM8 7h8v10a1 1 0 01-1 1H9a1 1 0 01-1-1V7z" />
                                </svg>
                                Expires {{ $item->expiry_date->format('M j, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" 
                    hx-get="{{ route('inventory.edit', $item) }}" 
                    hx-target="#inventory-modal"
                    class="text-blue-600 hover:text-blue-900">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
            <button type="button" 
                    hx-delete="{{ route('inventory.destroy', $item) }}" 
                    hx-target="closest li"
                    hx-swap="outerHTML"
                    hx-confirm="Are you sure you want to delete this item?"
                    class="text-red-600 hover:text-red-900">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
</li>