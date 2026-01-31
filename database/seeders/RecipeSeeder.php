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
            ['name' => 'Spaghetti', 'amount' => 400, 'unit_slug' => 'g'],
            ['name' => 'Eggs', 'amount' => 3, 'unit_slug' => 'piece'],
            ['name' => 'Parmesan cheese', 'amount' => 100, 'unit_slug' => 'g'],
            ['name' => 'Pancetta', 'amount' => 150, 'unit_slug' => 'g'],
            ['name' => 'Black pepper', 'amount' => 1, 'unit_slug' => 'tsp']
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
            ['name' => 'Chicken breast', 'amount' => 500, 'unit_slug' => 'g'],
            ['name' => 'Bell peppers', 'amount' => 2, 'unit_slug' => 'piece'],
            ['name' => 'Broccoli florets', 'amount' => 200, 'unit_slug' => 'g'],
            ['name' => 'Soy sauce', 'amount' => 3, 'unit_slug' => 'tbsp'],
            ['name' => 'Garlic', 'amount' => 3, 'unit_slug' => 'clove'],
            ['name' => 'Ginger', 'amount' => 15, 'unit_slug' => 'g'],
            ['name' => 'Vegetable oil', 'amount' => 2, 'unit_slug' => 'tbsp']
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
            ['name' => 'All-purpose flour', 'amount' => 300, 'unit_slug' => 'g'],
            ['name' => 'Butter', 'amount' => 200, 'unit_slug' => 'g'],
            ['name' => 'Brown sugar', 'amount' => 150, 'unit_slug' => 'g'],
            ['name' => 'White sugar', 'amount' => 100, 'unit_slug' => 'g'],
            ['name' => 'Eggs', 'amount' => 2, 'unit_slug' => 'piece'],
            ['name' => 'Vanilla extract', 'amount' => 2, 'unit_slug' => 'tsp'],
            ['name' => 'Baking soda', 'amount' => 1, 'unit_slug' => 'tsp'],
            ['name' => 'Salt', 'amount' => 0.5, 'unit_slug' => 'tsp'],
            ['name' => 'Chocolate chips', 'amount' => 200, 'unit_slug' => 'g']
        ]);

        // Create additional recipes that reuse ingredients
        $garlicBread = Recipe::create([
            'title' => 'Garlic Bread',
            'prep_time' => 10,
            'cook_time' => 15,
            'servings' => 6,
            'instructions' => [
                'Preheat oven to 375°F',
                'Mix butter, garlic, and herbs',
                'Slice bread and spread mixture',
                'Wrap in foil and bake for 15 minutes',
                'Unwrap and broil for 2-3 minutes until golden'
            ]
        ]);

        $this->attachIngredients($garlicBread, [
            ['name' => 'French bread', 'amount' => 1, 'unit_slug' => 'piece'],
            ['name' => 'Butter', 'amount' => 100, 'unit_slug' => 'g'],
            ['name' => 'Garlic', 'amount' => 4, 'unit_slug' => 'clove'],
            ['name' => 'Fresh parsley', 'amount' => 2, 'unit_slug' => 'tbsp'],
            ['name' => 'Salt', 'amount' => 0.25, 'unit_slug' => 'tsp']
        ]);

        $spaghettiAglio = Recipe::create([
            'title' => 'Spaghetti Aglio e Olio',
            'prep_time' => 5,
            'cook_time' => 12,
            'servings' => 4,
            'instructions' => [
                'Cook spaghetti in salted water until al dente',
                'Heat olive oil in large pan',
                'Add sliced garlic and red pepper flakes',
                'Toss drained pasta with oil mixture',
                'Add parsley and parmesan, serve immediately'
            ]
        ]);

        $this->attachIngredients($spaghettiAglio, [
            ['name' => 'Spaghetti', 'amount' => 1, 'unit_slug' => 'lb'],
            ['name' => 'Olive oil', 'amount' => 0.5, 'unit_slug' => 'cup'],
            ['name' => 'Garlic', 'amount' => 6, 'unit_slug' => 'clove'],
            ['name' => 'Red pepper flakes', 'amount' => 0.5, 'unit_slug' => 'tsp'],
            ['name' => 'Fresh parsley', 'amount' => 3, 'unit_slug' => 'tbsp'],
            ['name' => 'Parmesan cheese', 'amount' => 50, 'unit_slug' => 'g']
        ]);

        $scrambledEggs = Recipe::create([
            'title' => 'Perfect Scrambled Eggs',
            'prep_time' => 2,
            'cook_time' => 5,
            'servings' => 2,
            'instructions' => [
                'Crack eggs into bowl and whisk with salt',
                'Heat butter in non-stick pan over low heat',
                'Pour in eggs and stir gently',
                'Remove from heat while still creamy',
                'Season with pepper and serve'
            ]
        ]);

        $this->attachIngredients($scrambledEggs, [
            ['name' => 'Eggs', 'amount' => 6, 'unit_slug' => 'piece'],
            ['name' => 'Butter', 'amount' => 2, 'unit_slug' => 'tbsp'],
            ['name' => 'Salt', 'amount' => 0.25, 'unit_slug' => 'tsp'],
            ['name' => 'Black pepper', 'amount' => 0.125, 'unit_slug' => 'tsp']
        ]);

        // Create sample inventory with proper unit references
        $units = \App\Models\Unit::all()->keyBy('slug');
        
        Inventory::create([
            'name' => 'Spaghetti',
            'category' => 'pantry',
            'quantity' => 500,
            'unit_id' => $units->get('g')->id,
            'low_stock_threshold' => 100,
            'expiry_date' => now()->addMonths(12)
        ]);

        Inventory::create([
            'name' => 'Eggs',
            'category' => 'fridge',
            'quantity' => 12,
            'unit_id' => $units->get('piece')->id,
            'low_stock_threshold' => 3,
            'expiry_date' => now()->addDays(14)
        ]);

        Inventory::create([
            'name' => 'Chicken breast',
            'category' => 'fridge',
            'quantity' => 800,
            'unit_id' => $units->get('g')->id,
            'low_stock_threshold' => 200,
            'expiry_date' => now()->addDays(3)
        ]);

        Inventory::create([
            'name' => 'Bell peppers',
            'category' => 'fridge',
            'quantity' => 4,
            'unit_id' => $units->get('piece')->id,
            'low_stock_threshold' => 1,
            'expiry_date' => now()->addDays(7)
        ]);

        Inventory::create([
            'name' => 'All-purpose flour',
            'category' => 'pantry',
            'quantity' => 1000,
            'unit_id' => $units->get('g')->id,
            'low_stock_threshold' => 200,
            'expiry_date' => now()->addMonths(6)
        ]);

        Inventory::create([
            'name' => 'Butter',
            'category' => 'fridge',
            'quantity' => 500,
            'unit_id' => $units->get('g')->id,
            'low_stock_threshold' => 50,
            'expiry_date' => now()->addDays(30)
        ]);

        Inventory::create([
            'name' => 'Garlic',
            'category' => 'pantry',
            'quantity' => 8,
            'unit_id' => $units->get('clove')->id,
            'low_stock_threshold' => 2,
            'expiry_date' => now()->addDays(21)
        ]);

        Inventory::create([
            'name' => 'Olive oil',
            'category' => 'pantry',
            'quantity' => 500,
            'unit_id' => $units->get('ml')->id,
            'low_stock_threshold' => 100,
            'expiry_date' => now()->addMonths(18)
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
            // Create ingredient if it doesn't exist
            $ingredient = Ingredient::firstOrCreate(
                ['name' => $ingredientData['name']],
                ['default_unit_id' => $units->get($ingredientData['unit_slug'])?->id]
            );
            
            $unit = $units->get($ingredientData['unit_slug']);
            
            if ($ingredient && $unit) {
                try {
                    // Convert to canonical amount (base unit)
                    $canonicalAmount = $unitConverter->convertToCanonical(
                        $ingredientData['amount'], 
                        $unit, 
                        $ingredient
                    );
                    
                    $attachData[$ingredient->id] = [
                        'amount' => $ingredientData['amount'],
                        'unit' => $ingredientData['unit_slug'], // Keep old string for backward compatibility
                        'unit_id' => $unit->id,
                        'canonical_amount' => $canonicalAmount
                    ];
                } catch (\Exception $e) {
                    // Fallback if conversion fails
                    $attachData[$ingredient->id] = [
                        'amount' => $ingredientData['amount'],
                        'unit' => $ingredientData['unit_slug'],
                        'unit_id' => $unit->id,
                        'canonical_amount' => $ingredientData['amount'] // Use original amount as fallback
                    ];
                }
            }
        }
        
        $recipe->ingredients()->attach($attachData);
    }
}
