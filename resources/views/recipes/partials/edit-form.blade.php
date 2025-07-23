<!-- Modal Backdrop -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-data="{ open: true }" x-show="open">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3">
            <h3 class="text-lg font-medium text-gray-900">Edit Recipe</h3>
            <button type="button" 
                    @click="open = false; setTimeout(() => document.getElementById('recipe-modal').innerHTML = '', 300)"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form hx-put="{{ route('recipes.update', $recipe) }}" 
              hx-target="closest .bg-white" 
              hx-swap="outerHTML"
              hx-on::after-request="if(event.detail.successful) { open = false; setTimeout(() => document.getElementById('recipe-modal').innerHTML = '', 300) }">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Recipe Title</label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ $recipe->title }}"
                           required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="source_url" class="block text-sm font-medium text-gray-700">Source URL (optional)</label>
                    <input type="url" 
                           name="source_url" 
                           id="source_url"
                           value="{{ $recipe->source_url }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="prep_time" class="block text-sm font-medium text-gray-700">Prep Time (minutes)</label>
                        <input type="number" 
                               name="prep_time" 
                               id="prep_time" 
                               min="0"
                               value="{{ $recipe->prep_time }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="cook_time" class="block text-sm font-medium text-gray-700">Cook Time (minutes)</label>
                        <input type="number" 
                               name="cook_time" 
                               id="cook_time" 
                               min="0"
                               value="{{ $recipe->cook_time }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="servings" class="block text-sm font-medium text-gray-700">Servings</label>
                        <input type="number" 
                               name="servings" 
                               id="servings" 
                               min="1"
                               value="{{ $recipe->servings }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $recipe->notes }}</textarea>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-6 space-x-2">
                <button type="button" 
                        @click="open = false; setTimeout(() => document.getElementById('recipe-modal').innerHTML = '', 300)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Recipe
                </button>
            </div>
        </form>
    </div>
</div>