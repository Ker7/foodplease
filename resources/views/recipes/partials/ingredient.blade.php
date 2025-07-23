<div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center" id="ingredient-{{ $ingredient->id }}">
    <div class="flex-1">
        <span class="text-sm font-medium text-gray-900">{{ $ingredient->name }}</span>
        @if($ingredient->pivot && ($ingredient->pivot->amount || $ingredient->pivot->unit))
            @php
                $amount = $ingredient->pivot->amount ? number_format($ingredient->pivot->amount, 2) : '';
                $unit = $ingredient->pivot->unit ?: '';
                $formattedAmount = trim($amount . ($unit ? ' ' . $unit : ''));
            @endphp
            @if($formattedAmount)
                <span class="ml-2 text-sm text-gray-500">{{ $formattedAmount }}</span>
            @endif
        @endif
    </div>
    <div class="flex space-x-2">
        <button type="button" 
                hx-get="{{ route('recipes.ingredients.edit', [$recipe, $ingredient]) }}" 
                hx-target="#ingredient-form"
                class="text-blue-600 hover:text-blue-900">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </button>
        <button type="button" 
                hx-delete="{{ route('recipes.ingredients.destroy', [$recipe, $ingredient]) }}" 
                hx-target="#ingredient-{{ $ingredient->id }}"
                hx-swap="outerHTML"
                hx-confirm="Are you sure you want to remove this ingredient from the recipe?"
                class="text-red-600 hover:text-red-900">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </div>
</div>