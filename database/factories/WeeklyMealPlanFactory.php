<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklyMealPlan>
 */
class WeeklyMealPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $weekStart = Carbon::now()->startOfWeek()->addWeeks(fake()->numberBetween(-2, 4));

        return [
            'name' => 'Week of ' . $weekStart->format('M j'),
            'week_start' => $weekStart,
            'meals' => [],
            'is_active' => fake()->boolean(30) // 30% chance of being active
        ];
    }

    /**
     * Create an active meal plan
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create a meal plan for current week
     */
    public function currentWeek(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'This Week',
            'week_start' => Carbon::now()->startOfWeek(),
            'is_active' => true,
        ]);
    }

    /**
     * Create a meal plan with some meals already assigned
     * Note: This requires recipes to exist in the database
     */
    public function withMeals(array $meals = []): static
    {
        return $this->state(fn (array $attributes) => [
            'meals' => $meals,
        ]);
    }
}
