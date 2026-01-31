@if(count($mealPlan->getAllRecipesWithDuplicates()) > 0)
  <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">Meal Plan Overview</h3>
      <p class="mt-1 max-w-2xl text-sm text-gray-500">Statistics for this week's meal plan</p>
    </div>
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
      @php
        $totalRecipes = 0;
        $totalCookTime = 0;
        $totalPrepTime = 0;
        $uniqueIngredients = 0;

        // Get all recipes with duplicates for proper counting
        $allRecipes = $mealPlan->getAllRecipesWithDuplicates();

        foreach($allRecipes as $recipe) {
            $totalRecipes++;
            $totalCookTime += $recipe->cook_time ?? 0;
            $totalPrepTime += $recipe->prep_time ?? 0;
        }

        // Calculate unique ingredients (simplified)
        $uniqueIngredientNames = collect();
        foreach($allRecipes as $recipe) {
            $recipe->load(['ingredients']);
            foreach($recipe->ingredients as $ingredient) {
                $uniqueIngredientNames->push($ingredient->name);
            }
        }
        $uniqueIngredients = $uniqueIngredientNames->unique()->count();
      @endphp

      <div class="flex flex-wrap gap-4">
        <div class="bg-blue-50 rounded-lg p-4">
          <div class="text-2xl font-bold text-blue-600">{{ $totalRecipes }}</div>
          <div class="text-sm text-blue-800">Total Recipes</div>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
          <div class="text-2xl font-bold text-green-600">{{ $uniqueIngredients }}</div>
          <div class="text-sm text-green-800">Unique Ingredients</div>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4">
          <div class="text-2xl font-bold text-yellow-600">{{ $totalPrepTime }}m</div>
          <div class="text-sm text-yellow-800">Total Prep Time</div>
        </div>
        <div class="bg-red-50 rounded-lg p-4">
          <div class="text-2xl font-bold text-red-600">{{ $totalCookTime }}m</div>
          <div class="text-sm text-red-800">Total Cook Time</div>
        </div>
      </div>
    </div>
  </div>
@endif