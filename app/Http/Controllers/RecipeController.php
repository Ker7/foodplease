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
            'name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50'
        ]);

        // Find or create the ingredient
        $ingredient = Ingredient::firstOrCreate(
            ['name' => $validated['name']],
            ['default_unit' => $validated['unit']]
        );

        // Attach ingredient to recipe with amount and unit
        $recipe->ingredients()->syncWithoutDetaching([
            $ingredient->id => [
                'amount' => $validated['amount'],
                'unit' => $validated['unit']
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
            'name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50'
        ]);

        // If ingredient name changed, find or create new ingredient
        if ($ingredient->name !== $validated['name']) {
            $newIngredient = Ingredient::firstOrCreate(
                ['name' => $validated['name']],
                ['default_unit' => $validated['unit']]
            );
            
            // Remove old ingredient and add new one
            $recipe->ingredients()->detach($ingredient->id);
            $recipe->ingredients()->attach($newIngredient->id, [
                'amount' => $validated['amount'],
                'unit' => $validated['unit']
            ]);
            
            $ingredient = $newIngredient;
        } else {
            // Update pivot data only
            $recipe->ingredients()->updateExistingPivot($ingredient->id, [
                'amount' => $validated['amount'],
                'unit' => $validated['unit']
            ]);
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
