<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get unit references
        $units = \App\Models\Unit::all()->keyBy('slug');
        
        // Create all unique ingredients used across recipes
        $ingredients = [
            // Common pantry items
            ['name' => 'All-purpose flour', 'default_unit_slug' => 'g'],
            ['name' => 'Spaghetti', 'default_unit_slug' => 'g'],
            ['name' => 'Brown sugar', 'default_unit_slug' => 'g'],
            ['name' => 'White sugar', 'default_unit_slug' => 'g'],
            ['name' => 'Baking soda', 'default_unit_slug' => 'tsp'],
            ['name' => 'Salt', 'default_unit_slug' => 'tsp'],
            ['name' => 'Black pepper', 'default_unit_slug' => 'tsp'],
            ['name' => 'Vanilla extract', 'default_unit_slug' => 'tsp'],
            ['name' => 'Soy sauce', 'default_unit_slug' => 'tbsp'],
            ['name' => 'Vegetable oil', 'default_unit_slug' => 'tbsp'],
            ['name' => 'Chocolate chips', 'default_unit_slug' => 'g'],
            
            // Fresh/refrigerated items
            ['name' => 'Eggs', 'default_unit_slug' => 'piece'], // Changed from 'large' to 'piece'
            ['name' => 'Butter', 'default_unit_slug' => 'g'],
            ['name' => 'Parmesan cheese', 'default_unit_slug' => 'g'],
            ['name' => 'Pancetta', 'default_unit_slug' => 'g'],
            ['name' => 'Chicken breast', 'default_unit_slug' => 'g'],
            
            // Vegetables and herbs
            ['name' => 'Bell peppers', 'default_unit_slug' => 'piece'],
            ['name' => 'Broccoli florets', 'default_unit_slug' => 'g'],
            ['name' => 'Garlic', 'default_unit_slug' => 'clove'],
            ['name' => 'Ginger', 'default_unit_slug' => 'g'], // Changed from 'inch' to 'g'
            
            // Additional common ingredients for variety
            ['name' => 'Onion', 'default_unit_slug' => 'piece'],
            ['name' => 'Tomatoes', 'default_unit_slug' => 'piece'],
            ['name' => 'Olive oil', 'default_unit_slug' => 'tbsp'],
            ['name' => 'Lemon juice', 'default_unit_slug' => 'tbsp'],
            ['name' => 'Milk', 'default_unit_slug' => 'ml'],
            ['name' => 'Cheddar cheese', 'default_unit_slug' => 'g'],
            ['name' => 'Ground beef', 'default_unit_slug' => 'g'],
            ['name' => 'Rice', 'default_unit_slug' => 'g'],
            ['name' => 'Pasta sauce', 'default_unit_slug' => 'ml'],
            ['name' => 'Mushrooms', 'default_unit_slug' => 'g'],
            
            // Additional ingredients for new recipes
            ['name' => 'French bread', 'default_unit_slug' => 'piece'],
            ['name' => 'Fresh parsley', 'default_unit_slug' => 'tbsp'],
            ['name' => 'Red pepper flakes', 'default_unit_slug' => 'tsp'],
        ];

        foreach ($ingredients as $ingredientData) {
            $defaultUnit = $units->get($ingredientData['default_unit_slug']);
            
            Ingredient::firstOrCreate(
                ['name' => $ingredientData['name']],
                ['default_unit_id' => $defaultUnit ? $defaultUnit->id : null]
            );
        }
    }
}