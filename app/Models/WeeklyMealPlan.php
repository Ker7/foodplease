<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class WeeklyMealPlan extends Model
{
    protected $fillable = [
        'name',
        'week_start',
        'meals',
        'is_active'
    ];

    protected $casts = [
        'week_start' => 'date',
        'meals' => 'array',
        'is_active' => 'boolean'
    ];

    public function getWeekEndAttribute(): Carbon
    {
        return $this->week_start->copy()->addDays(6);
    }

    public function getMealForDay(string $day, string $mealType = 'dinner'): ?Recipe
    {
        $meals = $this->meals ?? [];
        $recipeId = $meals[$day][$mealType] ?? null;
        
        // Handle both single recipe (new format) and arrays (legacy format)
        if (is_array($recipeId)) {
            $recipeId = $recipeId[0] ?? null;
        }
        
        return $recipeId ? Recipe::find($recipeId) : null;
    }

    public function getMealsForDay(string $day, string $mealType = 'dinner'): array
    {
        $meals = $this->meals ?? [];
        $dayMeals = $meals[$day][$mealType] ?? [];
        
        // Handle both single recipe (old format) and multiple recipes (new format)
        if (is_numeric($dayMeals)) {
            return [$dayMeals];
        }
        
        return is_array($dayMeals) ? $dayMeals : [];
    }

    public function addMealForDay(string $day, string $mealType, int $recipeId): void
    {
        $meals = $this->meals ?? [];
        
        // Only allow one recipe per meal slot
        $meals[$day][$mealType] = $recipeId;
        $this->meals = $meals;
    }

    public function removeMealForDay(string $day, string $mealType, int $recipeId = null): void
    {
        $meals = $this->meals ?? [];
        
        // Remove the meal slot entirely
        unset($meals[$day][$mealType]);
        
        // Clean up empty day
        if (empty($meals[$day])) {
            unset($meals[$day]);
        }
        
        $this->meals = $meals;
    }

    public function setMealForDay(string $day, string $mealType, ?int $recipeId): void
    {
        $meals = $this->meals ?? [];
        
        if ($recipeId) {
            $meals[$day][$mealType] = $recipeId;
        } else {
            unset($meals[$day][$mealType]);
            if (empty($meals[$day])) {
                unset($meals[$day]);
            }
        }
        
        $this->meals = $meals;
    }

    public function getAllRecipes(): \Illuminate\Database\Eloquent\Collection
    {
        $recipeIds = [];
        foreach ($this->meals ?? [] as $day => $dayMeals) {
            foreach ($dayMeals as $mealType => $recipeData) {
                // Handle both single recipe (old format) and multiple recipes (new format)
                if (is_array($recipeData)) {
                    $recipeIds = array_merge($recipeIds, $recipeData);
                } elseif (is_numeric($recipeData)) {
                    $recipeIds[] = $recipeData;
                }
            }
        }
        
        $uniqueRecipeIds = array_unique(array_filter($recipeIds, 'is_numeric'));
        
        if (empty($uniqueRecipeIds)) {
            return new \Illuminate\Database\Eloquent\Collection();
            //return collect();
        }
        
        return Recipe::whereIn('id', $uniqueRecipeIds)->get();
    }

    public function getAllRecipesWithDuplicates(): array
    {
        $recipes = [];
        foreach ($this->meals ?? [] as $day => $dayMeals) {
            foreach ($dayMeals as $mealType => $recipeData) {
                // Handle both single recipe (new format) and arrays (legacy format)
                if (is_array($recipeData)) {
                    foreach ($recipeData as $recipeId) {
                        if (is_numeric($recipeId)) {
                            $recipe = Recipe::find($recipeId);
                            if ($recipe) {
                                $recipes[] = $recipe;
                            }
                        }
                    }
                } elseif (is_numeric($recipeData)) {
                    $recipe = Recipe::find($recipeData);
                    if ($recipe) {
                        $recipes[] = $recipe;
                    }
                }
            }
        }
        
        return $recipes;
    }
}
