<div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
    <div x-data="{ mode: $ingredient->pivot && $ingredient->pivot->unit_id ? 'existing' : 'new' }" class="space-y-4">
        <!-- Mode Selection -->
        <div class="flex space-x-4 border-b border-gray-200 pb-3">
            <button @click="mode = 'existing'" 
                    :class="mode === 'existing' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                    class="pb-2 border-b-2 font-medium text-sm transition-colors">
                Select Existing
            </button>
            <button @click="mode = 'new'" 
                    :class="mode === 'new' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'"
                    class="pb-2 border-b-2 font-medium text-sm transition-colors">
                Edit Name
            </button>
        </div>

        <!-- Select Existing Ingredient -->
        <div x-show="mode === 'existing'" x-transition>
            <form hx-put="{{ route('recipes.ingredients.update', [$recipe, $ingredient]) }}" 
                  hx-target="#ingredient-{{ $ingredient->id }}" 
                  hx-swap="outerHTML"
                  hx-on::after-request="if(event.detail.successful) { document.getElementById('ingredient-form').innerHTML = ''; }">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label for="existing_ingredient_id" class="block text-sm font-medium text-gray-700">Select Ingredient</label>
                        <select name="existing_ingredient_id" 
                                id="existing_ingredient_id" 
                                required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Choose an ingredient...</option>
                            @foreach(\App\Models\Ingredient::orderBy('name')->get() as $existingIngredient)
                                <option value="{{ $existingIngredient->id }}" 
                                        data-default-unit="{{ $existingIngredient->default_unit_id }}"
                                        {{ $existingIngredient->id == $ingredient->id ? 'selected' : '' }}>
                                    {{ $existingIngredient->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="existing_amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <input type="number" 
                                   name="amount" 
                                   id="existing_amount"
                                   step="0.01"
                                   min="0"
                                   value="{{ $ingredient->pivot ? $ingredient->pivot->amount : '' }}"
                                   required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="existing_unit_id" class="block text-sm font-medium text-gray-700">Unit</label>
                            <select name="unit_id" 
                                    id="existing_unit_id"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select unit...</option>
                                @foreach(\App\Models\Unit::orderBy('name')->get() as $unit)
                                    <option value="{{ $unit->id }}" {{ ($ingredient->pivot ? $ingredient->pivot->unit_id : null) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->slug }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" 
                            onclick="document.getElementById('ingredient-form').innerHTML = ''"
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Ingredient
                    </button>
                </div>
            </form>
        </div>

        <!-- Edit Ingredient Name -->
        <div x-show="mode === 'new'" x-transition>
            <form hx-put="{{ route('recipes.ingredients.update', [$recipe, $ingredient]) }}" 
                  hx-target="#ingredient-{{ $ingredient->id }}" 
                  hx-swap="outerHTML"
                  hx-on::after-request="if(event.detail.successful) { document.getElementById('ingredient-form').innerHTML = ''; }">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Ingredient Name</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               required
                               value="{{ $ingredient->name }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <input type="number" 
                                   name="amount" 
                                   id="amount"
                                   step="0.01"
                                   min="0"
                                   value="{{ $ingredient->pivot ? $ingredient->pivot->amount : '' }}"
                                   required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="unit_id" class="block text-sm font-medium text-gray-700">Unit</label>
                            <select name="unit_id" 
                                    id="unit_id"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select unit...</option>
                                @foreach(\App\Models\Unit::orderBy('name')->get() as $unit)
                                    <option value="{{ $unit->id }}" {{ ($ingredient->pivot ? $ingredient->pivot->unit_id : null) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->slug }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" 
                            onclick="document.getElementById('ingredient-form').innerHTML = ''"
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Update Ingredient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>