@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ $mealPlan->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Week of {{ $mealPlan->week_start->format('M j, Y') }} - {{ $mealPlan->week_end->format('M j, Y') }}
                @if($mealPlan->is_active)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Active Plan
                    </span>
                @endif
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <button type="button" 
                    hx-get="{{ route('meal-plans.edit', $mealPlan) }}" 
                    hx-target="#meal-plan-modal"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Edit Plan
            </button>
        </div>
    </div>

    <!-- Weekly Grid -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Weekly Schedule</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Plan your meals for each day of the week</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-7 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                @foreach($days as $day)
                    <div class="p-4">
                        <h4 class="text-sm font-medium text-gray-900 capitalize mb-3">{{ $day }}</h4>
                        <div class="space-y-2">
                            @foreach($mealTypes as $mealType)
                                <div id="meal-{{ $day }}-{{ $mealType }}">
                                    @include('meal-plans.partials.meal-slot', [
                                        'day' => $day,
                                        'mealType' => $mealType,
                                        'recipes' => $recipes,
                                        'mealPlan' => $mealPlan
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Shopping List Section -->
    @if($mealPlan->getAllRecipes()->count() > 0)
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Shopping List</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Ingredients needed for this week's meals</p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                @php
                    $unitConverter = new \App\Services\UnitConverter();
                    $aggregatedIngredients = collect();
                    $totalRecipes = 0;
                    $totalCookTime = 0;
                    $totalPrepTime = 0;
                    
                    // Get all recipes from the meal plan
                    foreach($mealPlan->meals ?? [] as $day => $dayMeals) {
                        foreach($dayMeals as $mealType => $recipeData) {
                            $recipeIds = is_array($recipeData) ? $recipeData : [$recipeData];
                            foreach($recipeIds as $recipeId) {
                                $recipe = \App\Models\Recipe::with(['ingredients.defaultUnit'])->find($recipeId);
                                if($recipe) {
                                    $totalRecipes++;
                                    $totalCookTime += $recipe->cook_time ?? 0;
                                    $totalPrepTime += $recipe->prep_time ?? 0;
                                    
                                    // Process each ingredient with unit conversion
                                    foreach($recipe->ingredients as $ingredient) {
                                        $ingredientName = $ingredient->name;
                                        $amount = $ingredient->pivot->amount ?? 0;
                                        $unit = $ingredient->pivot->unit_id ? \App\Models\Unit::find($ingredient->pivot->unit_id) : null;
                                        
                                        if ($amount > 0 && $unit) {
                                            // Convert to canonical amount (base unit)
                                            try {
                                                $canonicalAmount = $unitConverter->convertToCanonical($amount, $unit, $ingredient);
                                                $baseUnit = $unitConverter->getBaseUnitForType($unit->type);
                                                
                                                // Initialize or add to aggregated ingredients
                                                if (!$aggregatedIngredients->has($ingredientName)) {
                                                    $aggregatedIngredients->put($ingredientName, [
                                                        'name' => $ingredientName,
                                                        'canonical_amount' => 0,
                                                        'display_unit' => $ingredient->defaultUnit ?: $baseUnit,
                                                        'instances' => []
                                                    ]);
                                                }
                                                
                                                // Add canonical amount
                                                $current = $aggregatedIngredients->get($ingredientName);
                                                $current['canonical_amount'] += $canonicalAmount;
                                                $current['instances'][] = [
                                                    'amount' => $amount,
                                                    'unit' => $unit->slug,
                                                    'recipe' => $recipe->title
                                                ];
                                                $aggregatedIngredients->put($ingredientName, $current);
                                            } catch (\Exception $e) {
                                                // Fallback for unit conversion errors
                                                if (!$aggregatedIngredients->has($ingredientName)) {
                                                    $aggregatedIngredients->put($ingredientName, [
                                                        'name' => $ingredientName,
                                                        'canonical_amount' => 0,
                                                        'display_unit' => $unit,
                                                        'instances' => []
                                                    ]);
                                                }
                                                
                                                $current = $aggregatedIngredients->get($ingredientName);
                                                $current['instances'][] = [
                                                    'amount' => $amount,
                                                    'unit' => $unit->slug,
                                                    'recipe' => $recipe->title,
                                                    'error' => true
                                                ];
                                                $aggregatedIngredients->put($ingredientName, $current);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                @endphp
                
                <!-- Meal Plan Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600">{{ $totalRecipes }}</div>
                        <div class="text-sm text-blue-800">Total Recipes</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600">{{ $aggregatedIngredients->count() }}</div>
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
                
                <!-- Shopping List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($aggregatedIngredients as $ingredientData)
                        @php
                            $displayUnit = $ingredientData['display_unit'];
                            $canonicalAmount = $ingredientData['canonical_amount'];
                            $instances = $ingredientData['instances'];
                            
                            // Convert canonical amount back to display unit
                            $displayAmount = 0;
                            if ($canonicalAmount > 0 && $displayUnit) {
                                try {
                                    $displayAmount = $unitConverter->convertFromCanonical($canonicalAmount, $displayUnit);
                                } catch (\Exception $e) {
                                    $displayAmount = $canonicalAmount;
                                }
                            }
                        @endphp
                        <div class="flex items-start space-x-2 p-3 bg-gray-50 rounded-lg">
                            <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $ingredientData['name'] }}</div>
                                
                                @if($displayAmount > 0 && $displayUnit)
                                    <div class="text-sm text-gray-500">
                                        {{ $displayAmount == floor($displayAmount) ? number_format($displayAmount, 0) : number_format($displayAmount, 2) }} 
                                        {{ $displayUnit->slug ?? $displayUnit }}
                                    </div>
                                @endif
                                
                                @if(count($instances) > 1)
                                    <div class="text-xs text-gray-400 mt-1">
                                        From {{ count($instances) }} recipes:
                                        @foreach($instances as $instance)
                                            <span class="inline-block">
                                                {{ $instance['amount'] }}{{ $instance['unit'] }}
                                                @if(isset($instance['error']))
                                                    <span class="text-red-500">⚠</span>
                                                @endif
                                                @if(!$loop->last), @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal Container -->
<div id="meal-plan-modal"></div>
@endsection