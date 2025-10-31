<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dishTypes = ['Pasta', 'Soup', 'Salad', 'Sandwich', 'Stir-Fry', 'Casserole', 'Curry', 'Tacos', 'Pizza', 'Burger'];
        $adjectives = ['Delicious', 'Quick', 'Easy', 'Homemade', 'Classic', 'Spicy', 'Creamy', 'Healthy', 'Traditional'];

        return [
            'title' => fake()->randomElement($adjectives) . ' ' . fake()->randomElement($dishTypes),
            'source_url' => fake()->optional(0.6)->url(),
            'prep_time' => fake()->optional(0.9)->numberBetween(5, 45),
            'cook_time' => fake()->optional(0.9)->numberBetween(10, 90),
            'servings' => fake()->numberBetween(2, 8),
            'instructions' => [
                'Preheat and prepare ingredients',
                'Cook according to recipe',
                'Season to taste',
                'Serve hot'
            ],
            'notes' => fake()->optional(0.5)->sentence()
        ];
    }

    /**
     * Create a quick recipe (under 30 minutes total time)
     */
    public function quick(): static
    {
        return $this->state(fn (array $attributes) => [
            'prep_time' => fake()->numberBetween(5, 10),
            'cook_time' => fake()->numberBetween(10, 15),
        ]);
    }

    /**
     * Create a recipe with detailed instructions
     */
    public function detailed(): static
    {
        return $this->state(fn (array $attributes) => [
            'instructions' => [
                'Gather and measure all ingredients',
                'Preheat oven to 375°F (190°C)',
                'In a large bowl, combine dry ingredients',
                'In a separate bowl, mix wet ingredients',
                'Gradually fold wet ingredients into dry mixture',
                'Pour into prepared baking dish',
                'Bake for 30-35 minutes until golden',
                'Let cool for 10 minutes before serving'
            ],
            'notes' => fake()->paragraph()
        ]);
    }
}
