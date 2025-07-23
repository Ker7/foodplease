<!-- Modal Backdrop -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-data="{ open: true }" x-show="open">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3">
            <h3 class="text-lg font-medium text-gray-900">Create Weekly Meal Plan</h3>
            <button type="button" 
                    @click="open = false; setTimeout(() => document.getElementById('meal-plan-modal').innerHTML = '', 300)"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form hx-post="{{ route('meal-plans.store') }}" 
              hx-target="#meal-plans-list" 
              hx-swap="afterbegin"
              hx-on::after-request="if(event.detail.successful) { open = false; setTimeout(() => document.getElementById('meal-plan-modal').innerHTML = '', 300) }">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Plan Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           required
                           value="Weekly Plan - {{ now()->format('M j, Y') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="week_start" class="block text-sm font-medium text-gray-700">Week Starting</label>
                    <input type="date" 
                           name="week_start" 
                           id="week_start" 
                           required
                           value="{{ $weekStart->format('Y-m-d') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Set as active meal plan
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-6 space-x-2">
                <button type="button" 
                        @click="open = false; setTimeout(() => document.getElementById('meal-plan-modal').innerHTML = '', 300)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Meal Plan
                </button>
            </div>
        </form>
    </div>
</div>