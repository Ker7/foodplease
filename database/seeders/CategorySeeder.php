<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Recipe categories
        Category::create(['name' => 'Breakfast', 'type' => 'recipe', 'color' => '#F59E0B']);
        Category::create(['name' => 'Lunch', 'type' => 'recipe', 'color' => '#10B981']);
        Category::create(['name' => 'Dinner', 'type' => 'recipe', 'color' => '#3B82F6']);
        Category::create(['name' => 'Dessert', 'type' => 'recipe', 'color' => '#EC4899']);
        Category::create(['name' => 'Snacks', 'type' => 'recipe', 'color' => '#8B5CF6']);
        
        // Inventory categories
        Category::create(['name' => 'Proteins', 'type' => 'inventory', 'color' => '#EF4444']);
        Category::create(['name' => 'Vegetables', 'type' => 'inventory', 'color' => '#22C55E']);
        Category::create(['name' => 'Grains', 'type' => 'inventory', 'color' => '#F97316']);
        Category::create(['name' => 'Dairy', 'type' => 'inventory', 'color' => '#06B6D4']);
        Category::create(['name' => 'Spices', 'type' => 'inventory', 'color' => '#84CC16']);
        
        // Meal plan categories
        Category::create(['name' => 'Weekly Plan', 'type' => 'meal_plan', 'color' => '#6366F1']);
        Category::create(['name' => 'Special Diet', 'type' => 'meal_plan', 'color' => '#A855F7']);
        Category::create(['name' => 'Holiday Menu', 'type' => 'meal_plan', 'color' => '#F43F5E']);
    }
}
