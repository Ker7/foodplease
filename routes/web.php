<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WeeklyMealPlanController;

Route::get('/', [RecipeController::class, 'index'])->name('home');

// Recipe routes
Route::resource('recipes', RecipeController::class);
Route::get('recipes/{recipe}/ingredients/create', [RecipeController::class, 'createIngredient'])->name('recipes.ingredients.create');
Route::post('recipes/{recipe}/ingredients', [RecipeController::class, 'storeIngredient'])->name('recipes.ingredients.store');
Route::get('recipes/{recipe}/ingredients/{ingredient}/edit', [RecipeController::class, 'editIngredient'])->name('recipes.ingredients.edit');
Route::put('recipes/{recipe}/ingredients/{ingredient}', [RecipeController::class, 'updateIngredient'])->name('recipes.ingredients.update');
Route::delete('recipes/{recipe}/ingredients/{ingredient}', [RecipeController::class, 'destroyIngredient'])->name('recipes.ingredients.destroy');

// Inventory routes
Route::resource('inventory', InventoryController::class);

// Weekly meal plan routes
Route::resource('meal-plans', WeeklyMealPlanController::class);
Route::post('meal-plans/{mealPlan}/meals', [WeeklyMealPlanController::class, 'updateMeal'])->name('meal-plans.meals.update');
