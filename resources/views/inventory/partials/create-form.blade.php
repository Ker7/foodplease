<!-- Modal Backdrop -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-data="{ open: true }" x-show="open">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3">
            <h3 class="text-lg font-medium text-gray-900">Add Inventory Item</h3>
            <button type="button" 
                    @click="open = false; setTimeout(() => document.getElementById('inventory-modal').innerHTML = '', 300)"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form hx-post="{{ route('inventory.store') }}" 
              hx-target="#inventory-list" 
              hx-swap="afterbegin"
              hx-on::after-request="if(event.detail.successful) { open = false; setTimeout(() => document.getElementById('inventory-modal').innerHTML = '', 300) }">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Location</label>
                    <select name="category" 
                            id="category" 
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Select location...</option>
                        <option value="fridge">Fridge</option>
                        <option value="pantry">Pantry</option>
                        <option value="freezer">Freezer</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" 
                               name="quantity" 
                               id="quantity" 
                               step="0.01"
                               min="0"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                        <input type="text" 
                               name="unit" 
                               id="unit"
                               placeholder="g, pieces, cups, etc."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Specific Location (optional)</label>
                    <input type="text" 
                           name="location" 
                           id="location"
                           placeholder="Top shelf, door, etc."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                        <input type="date" 
                               name="expiry_date" 
                               id="expiry_date"
                               min="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700">Low Stock Alert</label>
                        <input type="number" 
                               name="low_stock_threshold" 
                               id="low_stock_threshold"
                               step="0.01"
                               min="0"
                               placeholder="Alert when quantity below..."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-6 space-x-2">
                <button type="button" 
                        @click="open = false; setTimeout(() => document.getElementById('inventory-modal').innerHTML = '', 300)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Add Item
                </button>
            </div>
        </form>
    </div>
</div>