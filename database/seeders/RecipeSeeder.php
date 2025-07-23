<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Inventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample recipes
        $spaghettiCarbonara = Recipe::create([
            'title' => 'Spaghetti Carbonara',
            'prep_time' => 10,
            'cook_time' => 15,
            'servings' => 4,
            'instructions' => [
                'Cook spaghetti according to package directions',
                'Whisk eggs, parmesan, and black pepper in a bowl',
                'Cook pancetta until crispy',
                'Toss hot pasta with egg mixture and pancetta',
                'Serve immediately with extra parmesan'
            ],
            'notes' => 'The key is to toss the pasta while it\'s hot so the eggs cook gently.'
        ]);

        // Attach ingredients to Spaghetti Carbonara
        $this->attachIngredients($spaghettiCarbonara, [
            ['name' => 'Spaghetti', 'amount' => 400, 'unit' => 'g'],
            ['name' => 'Eggs', 'amount' => 3, 'unit' => 'large'],
            ['name' => 'Parmesan cheese', 'amount' => 100, 'unit' => 'g'],
            ['name' => 'Pancetta', 'amount' => 150, 'unit' => 'g'],
            ['name' => 'Black pepper', 'amount' => 1, 'unit' => 'tsp']
        ]);

        $chickenStirFry = Recipe::create([
            'title' => 'Chicken Stir Fry',
            'prep_time' => 15,
            'cook_time' => 10,
            'servings' => 4,
            'instructions' => [
                'Cut chicken into bite-sized pieces',
                'Prepare all vegetables',
                'Heat oil in wok or large skillet',
                'Cook chicken until golden',
                'Add vegetables and stir-fry for 3-4 minutes',
                'Add sauce and toss to combine'
            ]
        ]);

        // Attach ingredients to Chicken Stir Fry
        $this->attachIngredients($chickenStirFry, [
            ['name' => 'Chicken breast', 'amount' => 500, 'unit' => 'g'],
            ['name' => 'Bell peppers', 'amount' => 2, 'unit' => 'pieces'],
            ['name' => 'Broccoli florets', 'amount' => 200, 'unit' => 'g'],
            ['name' => 'Soy sauce', 'amount' => 3, 'unit' => 'tbsp'],
            ['name' => 'Garlic', 'amount' => 3, 'unit' => 'cloves'],
            ['name' => 'Ginger', 'amount' => 1, 'unit' => 'inch'],
            ['name' => 'Vegetable oil', 'amount' => 2, 'unit' => 'tbsp']
        ]);

        $chocolateChipCookies = Recipe::create([
            'title' => 'Chocolate Chip Cookies',
            'prep_time' => 20,
            'cook_time' => 12,
            'servings' => 24,
            'instructions' => [
                'Preheat oven to 375°F',
                'Cream butter and sugars',
                'Beat in eggs and vanilla',
                'Mix in flour, baking soda, and salt',
                'Stir in chocolate chips',
                'Drop spoonfuls on baking sheet',
                'Bake for 9-12 minutes'
            ]
        ]);

        // Attach ingredients to Chocolate Chip Cookies
        $this->attachIngredients($chocolateChipCookies, [
            ['name' => 'All-purpose flour', 'amount' => 300, 'unit' => 'g'],
            ['name' => 'Butter', 'amount' => 200, 'unit' => 'g'],
            ['name' => 'Brown sugar', 'amount' => 150, 'unit' => 'g'],
            ['name' => 'White sugar', 'amount' => 100, 'unit' => 'g'],
            ['name' => 'Eggs', 'amount' => 2, 'unit' => 'large'],
            ['name' => 'Vanilla extract', 'amount' => 2, 'unit' => 'tsp'],
            ['name' => 'Baking soda', 'amount' => 1, 'unit' => 'tsp'],
            ['name' => 'Salt', 'amount' => 0.5, 'unit' => 'tsp'],
            ['name' => 'Chocolate chips', 'amount' => 200, 'unit' => 'g']
        ]);

        // Create sample inventory
        Inventory::create([
            'name' => 'Spaghetti',
            'category' => 'pantry',
            'quantity' => 500,
            'unit' => 'g',
            'low_stock_threshold' => 100,
            'expiry_date' => now()->addMonths(12)
        ]);

        Inventory::create([
            'name' => 'Eggs',
            'category' => 'fridge',
            'quantity' => 6,
            'unit' => 'pieces',
            'low_stock_threshold' => 2,
            'expiry_date' => now()->addDays(14)
        ]);

        Inventory::create([
            'name' => 'Chicken breast',
            'category' => 'fridge',
            'quantity' => 800,
            'unit' => 'g',
            'low_stock_threshold' => 200,
            'expiry_date' => now()->addDays(3)
        ]);

        Inventory::create([
            'name' => 'Bell peppers',
            'category' => 'fridge',
            'quantity' => 4,
            'unit' => 'pieces',
            'low_stock_threshold' => 1,
            'expiry_date' => now()->addDays(7)
        ]);

        Inventory::create([
            'name' => 'All-purpose flour',
            'category' => 'pantry',
            'quantity' => 1000,
            'unit' => 'g',
            'low_stock_threshold' => 200,
            'expiry_date' => now()->addMonths(6)
        ]);
    }

    /**
     * Helper method to attach ingredients to a recipe with pivot data
     */
    private function attachIngredients(Recipe $recipe, array $ingredients): void
    {
        $attachData = [];
        $units = \App\Models\Unit::all()->keyBy('slug');
        $unitConverter = new \App\Services\UnitConverter();
        
        foreach ($ingredients as $ingredientData) {
            $ingredient = Ingredient::where('name', $ingredientData['name'])->first();
            $unit = $units->get($ingredientData['unit']);
            
            if ($ingredient && $unit) {
                // Convert to canonical amount (base unit)
                $canonicalAmount = $unitConverter->convertToCanonical(
                    $ingredientData['amount'], 
                    $unit, 
                    $ingredient
                );
                
                $attachData[$ingredient->id] = [
                    'amount' => $ingredientData['amount'],
                    'unit' => $ingredientData['unit'], // Keep old string for backward compatibility
                    'unit_id' => $unit->id,
                    'canonical_amount' => $canonicalAmount
                ];
            }
        }
        
        $recipe->ingredients()->attach($attachData);
    }
}
