<div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
  @php
    $dayMeal = $mealPlan->meals[$day][$mealType] ?? null;
    // Handle both single recipe (new format) and arrays (legacy format)
    $recipeId = is_array($dayMeal) ? ($dayMeal[0] ?? null) : $dayMeal;
    $recipe = $recipeId ? \App\Models\Recipe::find($recipeId) : null;
    $hasRecipe = !is_null($recipe);
  @endphp

  <div class="flex items-center justify-between mb-2">
    <span class="text-xs font-medium text-gray-600 capitalize">{{ $mealType }}</span>
    @if($hasRecipe)
      <button type="button"
              hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
              hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "remove"}'
              hx-target="#meal-{{ $day }}-{{ $mealType }}"
              hx-swap="outerHTML"
              class="text-xs text-red-500 hover:text-red-700 px-2 py-1 border border-red-300 rounded hover:bg-red-50"
              title="Clear recipe">
        Clear
      </button>
    @endif
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
      <select name="recipe_id"
              hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
              hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "set"}'
              hx-target="#meal-{{ $day }}-{{ $mealType }}"
              hx-swap="outerHTML"
              hx-trigger="change"
              class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        <option value="">Change recipe...</option>
        @foreach($recipes as $availableRecipe)
          @if($availableRecipe->id !== $recipe->id)
            <option value="{{ $availableRecipe->id }}">{{ $availableRecipe->title }}</option>
          @endif
        @endforeach
      </select>
    @else
      <!-- Show dropdown when no recipe exists -->
      <select name="recipe_id"
              hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
              hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "set"}'
              hx-target="#meal-{{ $day }}-{{ $mealType }}"
              hx-swap="outerHTML"
              hx-trigger="change"
              class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        <option value="">Select recipe...</option>
        @foreach($recipes as $availableRecipe)
          <option value="{{ $availableRecipe->id }}">{{ $availableRecipe->title }}</option>
        @endforeach
      </select>
    @endif
  </div>
</div>