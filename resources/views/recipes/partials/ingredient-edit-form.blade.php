<div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
    <form hx-put="{{ route('recipes.ingredients.update', [$recipe, $ingredient]) }}" 
          hx-target="#ingredient-{{ $ingredient->id }}" 
          hx-swap="outerHTML"
          hx-on::after-request="if(event.detail.successful) { document.getElementById('ingredient-form').innerHTML = ''; }">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Ingredient</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       required
                       value="{{ $ingredient->name }}"
                       list="ingredients-datalist"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <datalist id="ingredients-datalist">
                    @foreach(\App\Models\Ingredient::orderBy('name')->get() as $existingIngredient)
                        <option value="{{ $existingIngredient->name }}">
                    @endforeach
                </datalist>
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                    <input type="text" 
                           name="unit" 
                           id="unit"
                           value="{{ $ingredient->pivot ? $ingredient->pivot->unit : '' }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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