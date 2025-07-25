<div id="meal-{{ $day }}-{{ $mealType }}" class="border border-gray-200 rounded-lg p-3 bg-gray-50">
  @php
    $dayMeal = $mealPlan->meals[$day][$mealType] ?? null;
    // Handle both single recipe (new format) and arrays (legacy format)
    $recipeId = is_array($dayMeal) ? ($dayMeal[0] ?? null) : $dayMeal;
    $recipe = $recipeId ? \App\Models\Recipe::find($recipeId) : null;
    $hasRecipe = !is_null($recipe);
  @endphp

  <div class="flex items-center justify-between mb-2">
    <span class="text-xs font-medium text-gray-600 capitalize">{{ $mealType }}</span>
    <div class="relative">
      @if($hasRecipe)
        <!-- Remove button -->
        <button type="button"
                hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
                hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "remove"}'
                hx-target="#meal-{{ $day }}-{{ $mealType }}"
                hx-swap="outerHTML"
                class="w-6 h-6 flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-red-50 rounded border border-red-300"
                title="Clear recipe">
          <svg class="w-3 h-3 htmx-indicator animate-spin text-green-500" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 7.071 2.929l-1.414 1.414A8 8 0 1 0 12 20a8 8 0 0 0 5.657-2.343l1.414 1.414A10 10 0 1 1 12 2z"></path>
          </svg>
          <svg class="w-3 h-3 htmx-not-indicator" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        <!-- Change recipe loader -->
        <div id="change-loader-{{ $day }}-{{ $mealType }}" class="htmx-indicator absolute right-8 top-0 w-4 h-4 flex items-center justify-center">
          <svg class="w-3 h-3 animate-spin text-green-500" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 7.071 2.929l-1.414 1.414A8 8 0 1 0 12 20a8 8 0 0 0 5.657-2.343l1.414 1.414A10 10 0 1 1 12 2z"></path>
          </svg>
        </div>
      @else
        <!-- Add button -->
        <button type="button"
                hx-get="{{ route('meal-plans.meals.show', $mealPlan) }}?day={{ $day }}&meal_type={{ $mealType }}&action=show_select"
                hx-target="#meal-{{ $day }}-{{ $mealType }}"
                hx-swap="outerHTML"
                class="w-6 h-6 flex items-center justify-center text-green-500 hover:text-green-700 hover:bg-green-50 rounded border border-green-300"
                title="Add recipe">
          <svg class="w-3 h-3 htmx-indicator animate-spin text-green-500" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 7.071 2.929l-1.414 1.414A8 8 0 1 0 12 20a8 8 0 0 0 5.657-2.343l1.414 1.414A10 10 0 1 1 12 2z"></path>
          </svg>
          <svg class="w-3 h-3 htmx-not-indicator" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
        </button>
      @endif
    </div>
  </div>

  <div class="space-y-2">
    @if($hasRecipe)
      <!-- Show current recipe -->
      <div class="bg-white rounded p-2 text-sm">
        <div class="font-medium text-gray-900 break-words">{{ $recipe->title }}</div>
        @if($recipe->prep_time || $recipe->cook_time)
          <div class="text-xs text-gray-500 mt-1">
            @if($recipe->prep_time)
              {{ $recipe->prep_time }}m prep
            @endif
            @if($recipe->prep_time && $recipe->cook_time)
              •
            @endif
            @if($recipe->cook_time)
              {{ $recipe->cook_time }}m cook
            @endif
          </div>
        @endif
      </div>

      <!-- Change recipe dropdown -->
      <div class="relative">
        <select name="recipe_id"
                hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
                hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "set"}'
                hx-target="#meal-{{ $day }}-{{ $mealType }}"
                hx-swap="outerHTML"
                hx-trigger="change"
                hx-indicator="#change-loader-{{ $day }}-{{ $mealType }}"
                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
          <option value="" disabled>Change recipe...</option>
          @foreach($recipes as $availableRecipe)
            @if($availableRecipe->id !== $recipe->id)
              <option value="{{ $availableRecipe->id }}">{{ $availableRecipe->title }}</option>
            @endif
          @endforeach
        </select>
      </div>
    @else
      <!-- No recipe state - show message only -->
      <div class="text-center py-6 text-gray-500 text-sm">
        No recipe selected
      </div>
    @endif
  </div>
</div>