<div id="meal-{{ $day }}-{{ $mealType }}" class="border border-gray-200 rounded-lg p-3 bg-gray-50">
  <div class="flex items-center justify-between mb-2">
    <span class="text-xs font-medium text-gray-600 capitalize">{{ $mealType }}</span>
    <div class="relative">
      <!-- Cancel button -->
      <button type="button"
              hx-get="{{ route('meal-plans.meals.show', $mealPlan) }}?day={{ $day }}&meal_type={{ $mealType }}&action=cancel_select"
              hx-target="#meal-{{ $day }}-{{ $mealType }}"
              hx-swap="outerHTML"
              class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded border border-gray-300"
              title="Cancel">
        <svg class="w-3 h-3 htmx-indicator animate-spin text-green-500" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 7.071 2.929l-1.414 1.414A8 8 0 1 0 12 20a8 8 0 0 0 5.657-2.343l1.414 1.414A10 10 0 1 1 12 2z"></path>
        </svg>
        <svg class="w-3 h-3 htmx-not-indicator" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      <!-- Select recipe loader -->
      <div id="select-loader-{{ $day }}-{{ $mealType }}" class="htmx-indicator absolute right-8 top-0 w-4 h-4 flex items-center justify-center">
        <svg class="w-3 h-3 animate-spin text-green-500" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 7.071 2.929l-1.414 1.414A8 8 0 1 0 12 20a8 8 0 0 0 5.657-2.343l1.414 1.414A10 10 0 1 1 12 2z"></path>
        </svg>
      </div>
    </div>
  </div>

  <div class="space-y-2">
    <!-- Recipe selection dropdown -->
    <div class="relative">
      <select name="recipe_id"
              hx-post="{{ route('meal-plans.meals.update', $mealPlan) }}"
              hx-vals='{"day": "{{ $day }}", "meal_type": "{{ $mealType }}", "action": "set"}'
              hx-target="#meal-{{ $day }}-{{ $mealType }}"
              hx-swap="outerHTML"
              hx-trigger="change"
              hx-indicator="#select-loader-{{ $day }}-{{ $mealType }}"
              class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              autofocus>
        <option value="" disabled>Select recipe...</option>
        @foreach($recipes as $availableRecipe)
          <option value="{{ $availableRecipe->id }}">{{ $availableRecipe->title }}</option>
        @endforeach
      </select>
    </div>
  </div>
</div>