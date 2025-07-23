<div class="border border-gray-200 rounded-lg p-3 bg-gray-50" x-data="{ showForm: false }">
  @php
    $dayMeals = $mealPlan->meals[$day][$mealType] ?? [];
    $recipeIds = is_array($dayMeals) ? array_filter($dayMeals, 'is_numeric') : ($dayMeals ? [$dayMeals] : []);
    $hasRecipes = !empty($recipeIds);
  @endphp

  <div class="flex items-center justify-between mb-2">
    <span class="text-xs font-medium text-gray-600 capitalize">{{ $mealType }}</span>

    @if($hasRecipes)
      <button type="button"
              @click="showForm = !showForm"
              x-show="!showForm"
              class="text-blue-500 hover:text-blue-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
      </button>
    @endif
  </div>

  <div class="space-y-2">
    <!-- Show existing recipes -->
    @if($hasRecipes)
      @foreach($recipeIds as $recipeId)
        @php $recipe = \App\Models\Recipe::find($recipeId) @endphp
        @if($recipe)
          <div class="flex items-center justify-between bg-white rounded p-2 text-sm">
            <div class="flex-1">
              <div class="font-medium text-gray-900 truncate">{{ $recipe->title }}</div>
              @if($recipe->prep_time || $recipe->cook_time)
                <div class="text-xs text-gray-500">
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
            <button type="button"
                    hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
                    hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "remove", "remove_recipe_id": {{ $recipe->id }}}'
                    hx-target="#meal-{{ $day }}-{{ $mealType }}"
                    hx-swap="outerHTML"
                    class="text-red-500 hover:text-red-700 ml-2">
              <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        @endif
      @endforeach

      <!-- Add another recipe form -->
      <div x-show="showForm" x-collapse>
        <select name="recipe_id"
                hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
                hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "add"}'
                hx-target="#meal-{{ $day }}-{{ $mealType }}"
                hx-swap="outerHTML"
                hx-trigger="change"
                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
          <option value="">Add another recipe...</option>
          @foreach($recipes as $availableRecipe)
            @if(!in_array($availableRecipe->id, $recipeIds))
              <option value="{{ $availableRecipe->id }}">{{ $availableRecipe->title }}</option>
            @endif
          @endforeach
        </select>
      </div>
    @else
      <!-- Show dropdown when no recipes exist -->
      <select name="recipe_id"
              hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
              hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "add"}'
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