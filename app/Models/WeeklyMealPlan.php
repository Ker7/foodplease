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
        $currentMeals = $this->getMealsForDay($day, $mealType);
        
        // Ensure we have a flat array of numeric recipe IDs
        $currentMeals = array_values(array_filter($currentMeals, 'is_numeric'));
        
        if (!in_array($recipeId, $currentMeals)) {
            $currentMeals[] = $recipeId;
            $meals[$day][$mealType] = array_values($currentMeals);
            $this->meals = $meals;
        }
    }

    public function removeMealForDay(string $day, string $mealType, int $recipeId): void
    {
        $meals = $this->meals ?? [];
        $currentMeals = $this->getMealsForDay($day, $mealType);
        
        $currentMeals = array_filter($currentMeals, fn($id) => $id != $recipeId);
        
        if (empty($currentMeals)) {
            unset($meals[$day][$mealType]);
            if (empty($meals[$day])) {
                unset($meals[$day]);
            }
        } else {
            $meals[$day][$mealType] = array_values($currentMeals);
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
}
