<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $recipes = Recipe::with('ingredients')->latest()->get();
        
        if ($request->header('HX-Request')) {
            return view('recipes.partials.list', compact('recipes'));
        }
        
        return view('recipes.index', compact('recipes'));
    }

    public function create(Request $request)
    {
        if ($request->header('HX-Request')) {
            return view('recipes.partials.create-form');
        }
        
        return view('recipes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source_url' => 'nullable|url',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'instructions' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        $recipe = Recipe::create($validated);

        if ($request->header('HX-Request')) {
            return view('recipes.partials.card', compact('recipe'));
        }

        return redirect()->route('recipes.show', $recipe)->with('success', 'Recipe created successfully!');
    }

    public function show(Recipe $recipe, Request $request)
    {
        $recipe->load('ingredients');
        
        if ($request->header('HX-Request')) {
            return view('recipes.partials.show', compact('recipe'));
        }
        
        return view('recipes.show', compact('recipe'));
    }

    public function edit(Recipe $recipe, Request $request)
    {
        if ($request->header('HX-Request')) {
            return view('recipes.partials.edit-form', compact('recipe'));
        }
        
        return view('recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source_url' => 'nullable|url',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'instructions' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        $recipe->update($validated);

        if ($request->header('HX-Request')) {
            return view('recipes.partials.card', compact('recipe'));
        }

        return redirect()->route('recipes.show', $recipe)->with('success', 'Recipe updated successfully!');
    }

    public function destroy(Recipe $recipe, Request $request)
    {
        $recipe->delete();

        if ($request->header('HX-Request')) {
            return response('', 200);
        }

        return redirect()->route('recipes.index')->with('success', 'Recipe deleted successfully!');
    }

    public function createIngredient(Request $request, Recipe $recipe)
    {
        if ($request->header('HX-Request')) {
            return view('recipes.partials.ingredient-form', compact('recipe'));
        }
        
        return redirect()->route('recipes.show', $recipe);
    }

    public function storeIngredient(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'existing_ingredient_id' => 'nullable|exists:ingredients,id',
            'amount' => 'nullable|numeric|min:0',
            'unit_id' => 'required|exists:units,id'
        ]);

        $unit = \App\Models\Unit::find($validated['unit_id']);
        $unitConverter = new \App\Services\UnitConverter();

        // Determine which ingredient to use
        if (!empty($validated['existing_ingredient_id'])) {
            // Using existing ingredient
            $ingredient = Ingredient::find($validated['existing_ingredient_id']);
        } else {
            // Creating new ingredient
            if (empty($validated['name'])) {
                return back()->withErrors(['name' => 'Ingredient name is required when creating a new ingredient.']);
            }

            $ingredient = Ingredient::firstOrCreate(
                ['name' => $validated['name']],
                ['default_unit_id' => $validated['unit_id']]
            );
        }

        // Convert to canonical amount
        $canonicalAmount = 0;
        if (!empty($validated['amount']) && $validated['amount'] > 0) {
            try {
                $canonicalAmount = $unitConverter->convertToCanonical(
                    $validated['amount'], 
                    $unit, 
                    $ingredient
                );
            } catch (\Exception $e) {
                $canonicalAmount = $validated['amount'];
            }
        }

        // Attach ingredient to recipe with amount, unit, and canonical amount
        $recipe->ingredients()->syncWithoutDetaching([
            $ingredient->id => [
                'amount' => $validated['amount'] ?? 0,
                'unit' => $unit->slug, // Keep old field for backward compatibility
                'unit_id' => $validated['unit_id'],
                'canonical_amount' => $canonicalAmount
            ]
        ]);

        // Reload the ingredient with pivot data
        $ingredient = $recipe->ingredients()->where('ingredient_id', $ingredient->id)->first();

        if ($request->header('HX-Request')) {
            return view('recipes.partials.ingredient', compact('ingredient', 'recipe'));
        }

        return back()->with('success', 'Ingredient added successfully!');
    }

    public function editIngredient(Request $request, Recipe $recipe, Ingredient $ingredient)
    {
        if ($request->header('HX-Request')) {
            return view('recipes.partials.ingredient-edit-form', compact('recipe', 'ingredient'));
        }
        
        return redirect()->route('recipes.show', $recipe);
    }

    public function updateIngredient(Request $request, Recipe $recipe, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'existing_ingredient_id' => 'nullable|exists:ingredients,id',
            'amount' => 'nullable|numeric|min:0',
            'unit_id' => 'required|exists:units,id'
        ]);

        $unit = \App\Models\Unit::find($validated['unit_id']);
        $unitConverter = new \App\Services\UnitConverter();

        // Determine which ingredient to use
        if ($validated['existing_ingredient_id']) {
            // Using existing ingredient - check if it's different from current
            $newIngredient = Ingredient::find($validated['existing_ingredient_id']);
            
            if ($newIngredient->id !== $ingredient->id) {
                // Remove old ingredient and add new one
                $recipe->ingredients()->detach($ingredient->id);
                
                // Convert to canonical amount
                $canonicalAmount = 0;
                if ($validated['amount'] > 0) {
                    try {
                        $canonicalAmount = $unitConverter->convertToCanonical(
                            $validated['amount'], 
                            $unit, 
                            $newIngredient
                        );
                    } catch (\Exception $e) {
                        $canonicalAmount = $validated['amount'];
                    }
                }
                
                $recipe->ingredients()->attach($newIngredient->id, [
                    'amount' => $validated['amount'],
                    'unit' => $unit->slug,
                    'unit_id' => $validated['unit_id'],
                    'canonical_amount' => $canonicalAmount
                ]);
                
                $ingredient = $newIngredient;
            } else {
                // Same ingredient, just update pivot data
                $canonicalAmount = 0;
                if ($validated['amount'] > 0) {
                    try {
                        $canonicalAmount = $unitConverter->convertToCanonical(
                            $validated['amount'], 
                            $unit, 
                            $ingredient
                        );
                    } catch (\Exception $e) {
                        $canonicalAmount = $validated['amount'];
                    }
                }
                
                $recipe->ingredients()->updateExistingPivot($ingredient->id, [
                    'amount' => $validated['amount'],
                    'unit' => $unit->slug,
                    'unit_id' => $validated['unit_id'],
                    'canonical_amount' => $canonicalAmount
                ]);
            }
        } else {
            // Creating/updating ingredient name
            if (!$validated['name']) {
                return back()->withErrors(['name' => 'Ingredient name is required when editing the ingredient name.']);
            }
            
            if ($ingredient->name !== $validated['name']) {
                $newIngredient = Ingredient::firstOrCreate(
                    ['name' => $validated['name']],
                    ['default_unit_id' => $validated['unit_id']]
                );
                
                // Convert to canonical amount
                $canonicalAmount = 0;
                if ($validated['amount'] > 0) {
                    try {
                        $canonicalAmount = $unitConverter->convertToCanonical(
                            $validated['amount'], 
                            $unit, 
                            $newIngredient
                        );
                    } catch (\Exception $e) {
                        $canonicalAmount = $validated['amount'];
                    }
                }
                
                // Remove old ingredient and add new one
                $recipe->ingredients()->detach($ingredient->id);
                $recipe->ingredients()->attach($newIngredient->id, [
                    'amount' => $validated['amount'],
                    'unit' => $unit->slug,
                    'unit_id' => $validated['unit_id'],
                    'canonical_amount' => $canonicalAmount
                ]);
                
                $ingredient = $newIngredient;
            } else {
                // Same name, just update pivot data
                $canonicalAmount = 0;
                if ($validated['amount'] > 0) {
                    try {
                        $canonicalAmount = $unitConverter->convertToCanonical(
                            $validated['amount'], 
                            $unit, 
                            $ingredient
                        );
                    } catch (\Exception $e) {
                        $canonicalAmount = $validated['amount'];
                    }
                }
                
                $recipe->ingredients()->updateExistingPivot($ingredient->id, [
                    'amount' => $validated['amount'],
                    'unit' => $unit->slug,
                    'unit_id' => $validated['unit_id'],
                    'canonical_amount' => $canonicalAmount
                ]);
            }
        }

        // Reload ingredient with pivot data
        $ingredient = $recipe->ingredients()->where('ingredient_id', $ingredient->id)->first();

        if ($request->header('HX-Request')) {
            return view('recipes.partials.ingredient', compact('ingredient', 'recipe'));
        }

        return back()->with('success', 'Ingredient updated successfully!');
    }

    public function destroyIngredient(Request $request, Recipe $recipe, Ingredient $ingredient)
    {
        // Detach ingredient from recipe (don't delete the ingredient itself)
        $recipe->ingredients()->detach($ingredient->id);

        if ($request->header('HX-Request')) {
            return response('', 200);
        }

        return back()->with('success', 'Ingredient removed from recipe successfully!');
    }
}
