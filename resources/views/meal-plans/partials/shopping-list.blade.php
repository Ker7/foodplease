@if(count($mealPlan->getAllRecipesWithDuplicates()) > 0)
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
                $debugInfo = [];
                
                // Debug: Check if meal plan has any meals
                $debugInfo['meal_plan_meals'] = $mealPlan->meals ?? [];
                $debugInfo['meal_plan_id'] = $mealPlan->id;
                
                // Get all recipes with duplicates for proper ingredient aggregation
                $allRecipes = $mealPlan->getAllRecipesWithDuplicates();
                $debugInfo['all_recipes_count'] = count($allRecipes);
                $debugInfo['all_recipes'] = collect($allRecipes)->map(function($recipe) {
                    return $recipe->title;
                })->toArray();
                
                foreach($allRecipes as $recipe) {
                    $totalRecipes++;
                    $totalCookTime += $recipe->cook_time ?? 0;
                    $totalPrepTime += $recipe->prep_time ?? 0;
                    
                    // Load ingredients with pivot data and units
                    $recipe->load(['ingredients' => function($query) {
                        $query->withPivot(['amount', 'unit', 'unit_id', 'canonical_amount']);
                    }, 'ingredients.defaultUnit']);
                    
                    $debugInfo['recipe_' . $recipe->id] = [
                        'title' => $recipe->title,
                        'ingredients_count' => $recipe->ingredients->count(),
                        'ingredients' => $recipe->ingredients->map(function($ing) {
                            return [
                                'name' => $ing->name,
                                'amount' => $ing->pivot->amount ?? 'null',
                                'unit_id' => $ing->pivot->unit_id ?? 'null',
                                'unit_slug' => $ing->pivot->unit ?? 'null'
                            ];
                        })->toArray()
                    ];
                    
                    // Process each ingredient with unit conversion
                    foreach($recipe->ingredients as $ingredient) {
                        $ingredientName = $ingredient->name;
                        $amount = $ingredient->pivot->amount ?? 0;
                        $unit = null;
                        
                        // Try to get unit from unit_id first, then fallback to unit slug
                        if ($ingredient->pivot->unit_id) {
                            $unit = \App\Models\Unit::find($ingredient->pivot->unit_id);
                        } elseif ($ingredient->pivot->unit) {
                            $unit = \App\Models\Unit::where('slug', $ingredient->pivot->unit)->first();
                        }
                        
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
                                    'error' => true,
                                    'error_message' => $e->getMessage()
                                ];
                                $aggregatedIngredients->put($ingredientName, $current);
                            }
                        } elseif ($amount > 0) {
                            // Handle ingredients without units (fallback)
                            if (!$aggregatedIngredients->has($ingredientName)) {
                                $aggregatedIngredients->put($ingredientName, [
                                    'name' => $ingredientName,
                                    'canonical_amount' => 0,
                                    'display_unit' => null,
                                    'instances' => []
                                ]);
                            }
                            
                            $current = $aggregatedIngredients->get($ingredientName);
                            $current['instances'][] = [
                                'amount' => $amount,
                                'unit' => 'no unit',
                                'recipe' => $recipe->title,
                                'warning' => 'No unit specified'
                            ];
                            $aggregatedIngredients->put($ingredientName, $current);
                        }
                    }
                }
                
                $debugInfo['aggregated_ingredients_count'] = $aggregatedIngredients->count();
                
                // Group ingredients by typical storage location
                $ingredientsByLocation = collect();
                $locationCategories = [
                    'fridge' => ['name' => 'Fridge', 'icon' => '🧊', 'color' => 'blue'],
                    'pantry' => ['name' => 'Pantry', 'icon' => '🍽️', 'color' => 'green'],
                    'freezer' => ['name' => 'Freezer', 'icon' => '❄️', 'color' => 'indigo']
                ];
                
                // Define ingredient storage mapping
                $ingredientStorageMap = [
                    // Fridge items
                    'fridge' => ['eggs', 'butter', 'milk', 'chicken breast', 'bell peppers', 'broccoli florets', 'fresh parsley', 'garlic', 'cheddar cheese', 'parmesan cheese', 'pancetta'],
                    // Pantry items  
                    'pantry' => ['spaghetti', 'all-purpose flour', 'brown sugar', 'white sugar', 'baking soda', 'salt', 'black pepper', 'vanilla extract', 'soy sauce', 'vegetable oil', 'chocolate chips', 'olive oil', 'red pepper flakes', 'french bread', 'ginger'],
                    // Freezer items
                    'freezer' => ['ground beef']
                ];
                
                foreach($aggregatedIngredients as $ingredientData) {
                    $ingredientName = strtolower($ingredientData['name']);
                    $location = 'pantry'; // default
                    
                    // Find the appropriate storage location
                    foreach($ingredientStorageMap as $storageLocation => $ingredients) {
                        if (in_array($ingredientName, $ingredients)) {
                            $location = $storageLocation;
                            break;
                        }
                    }
                    
                    if (!$ingredientsByLocation->has($location)) {
                        $ingredientsByLocation->put($location, collect());
                    }
                    
                    $ingredientsByLocation->get($location)->push($ingredientData);
                }
            @endphp
            
            <!-- Debug Information (temporary) -->
            @if(config('app.debug'))
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-medium text-yellow-800 mb-2">Debug Info:</h4>
                    <div class="text-sm text-yellow-700">
                        <p><strong>Meal Plan ID:</strong> {{ $debugInfo['meal_plan_id'] }}</p>
                        <p><strong>Raw Meals Data:</strong> {{ json_encode($debugInfo['meal_plan_meals']) }}</p>
                        <p><strong>Recipes Found:</strong> {{ $debugInfo['all_recipes_count'] }}</p>
                        <p><strong>Recipe Titles:</strong> {{ json_encode($debugInfo['all_recipes']) }}</p>
                        <p><strong>Aggregated Ingredients:</strong> {{ $debugInfo['aggregated_ingredients_count'] }}</p>
                    </div>
                </div>
            @endif

            
            <!-- Shopping List -->
            @if($aggregatedIngredients->count() > 0)
                @foreach($locationCategories as $locationKey => $locationInfo)
                    @if($ingredientsByLocation->has($locationKey) && $ingredientsByLocation->get($locationKey)->count() > 0)
                        <div class="mb-8">
                            <div class="bg-{{ $locationInfo['color'] }}-50 px-4 py-3 border-l-4 border-{{ $locationInfo['color'] }}-400 mb-4">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">{{ $locationInfo['icon'] }}</span>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ $locationInfo['name'] }}</h3>
                                        <p class="text-sm text-gray-600">{{ $ingredientsByLocation->get($locationKey)->count() }} ingredients</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($ingredientsByLocation->get($locationKey) as $ingredientData)
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
                                // Fallback: show canonical amount with base unit
                                $displayAmount = $canonicalAmount;
                                $baseUnit = $unitConverter->getBaseUnitForType($displayUnit->type);
                                $displayUnit = $baseUnit;
                            }
                        }
                        
                        // Debug: show calculation details if there are multiple instances
                        $showCalculation = count($instances) > 1;
                    @endphp
                    <div class="flex items-start space-x-2 p-3 bg-gray-50 rounded-lg">
                        <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $ingredientData['name'] }}</div>
                            
                            @if($displayAmount > 0 && $displayUnit)
                                <div class="text-sm font-medium text-blue-600">
                                    Total: {{ $displayAmount == floor($displayAmount) ? number_format($displayAmount, 0) : number_format($displayAmount, 2) }} 
                                    {{ is_object($displayUnit) ? $displayUnit->slug : $displayUnit }}
                                </div>
                            @elseif($canonicalAmount > 0)
                                <div class="text-sm font-medium text-blue-600">
                                    Total: {{ $canonicalAmount == floor($canonicalAmount) ? number_format($canonicalAmount, 0) : number_format($canonicalAmount, 2) }} 
                                    (base units)
                                </div>
                            @endif
                            
                            @if(count($instances) >= 1)
                                <div class="text-xs text-gray-400 mt-1">
                                    @if(count($instances) > 1)
                                        <p>Combined from {{ count($instances) }} recipes:</p>
                                    @else
                                        From recipe:
                                    @endif
                                    @foreach($instances as $instance)
                                        <span class="block">
                                            {{ $instance['amount'] }}{{ $instance['unit'] }}
                                            @if(isset($instance['recipe']))
                                                <span class="text-gray-300">({{ $instance['recipe'] }})</span>
                                            @endif
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
                    @endif
                @endforeach
            @else
                <div class="text-center py-12">
                    <div class="text-gray-500 text-lg mb-2">No ingredients found</div>
                    <div class="text-sm text-gray-400">
                        @if($totalRecipes == 0)
                            Add some recipes to your meal plan to see ingredients here.
                        @else
                            The recipes in your meal plan don't have any ingredients with proper units set up.
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif