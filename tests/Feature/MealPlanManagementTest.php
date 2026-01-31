<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\WeeklyMealPlan;
use App\Models\Recipe;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MealPlanManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_view_meal_plans_index()
    {
        WeeklyMealPlan::factory()->count(3)->create();

        $response = $this->get(route('meal-plans.index'));

        $response->assertStatus(200);
        $response->assertViewIs('meal-plans.index');
        $response->assertViewHas('mealPlans');
    }

    /** @test */
    public function it_can_view_meal_plans_index_as_htmx_request()
    {
        WeeklyMealPlan::factory()->count(2)->create();

        $response = $this->get(route('meal-plans.index'), ['HX-Request' => 'true']);

        $response->assertStatus(200);
        $response->assertViewIs('meal-plans.partials.list');
    }

    /** @test */
    public function it_can_create_a_meal_plan()
    {
        $weekStart = Carbon::now()->startOfWeek();

        $response = $this->post(route('meal-plans.store'), [
            'name' => 'Week of January',
            'week_start' => $weekStart->format('Y-m-d'),
            'is_active' => true
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('weekly_meal_plans', [
            'name' => 'Week of January',
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_requires_name_and_week_start()
    {
        $response = $this->post(route('meal-plans.store'), []);

        $response->assertSessionHasErrors(['name', 'week_start']);
    }

    /** @test */
    public function it_can_view_a_single_meal_plan()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();

        $response = $this->get(route('meal-plans.show', $mealPlan));

        $response->assertStatus(200);
        $response->assertViewIs('meal-plans.show');
        $response->assertViewHas('mealPlan');
        $response->assertSee($mealPlan->name);
    }

    /** @test */
    public function it_can_update_a_meal_plan()
    {
        $mealPlan = WeeklyMealPlan::factory()->create(['name' => 'Old Name']);

        $response = $this->put(route('meal-plans.update', $mealPlan), [
            'name' => 'New Name',
            'week_start' => $mealPlan->week_start->format('Y-m-d'),
            'is_active' => false
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('weekly_meal_plans', [
            'id' => $mealPlan->id,
            'name' => 'New Name',
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_can_delete_a_meal_plan()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();

        $response = $this->delete(route('meal-plans.destroy', $mealPlan));

        $response->assertRedirect(route('meal-plans.index'));
        $this->assertDatabaseMissing('weekly_meal_plans', ['id' => $mealPlan->id]);
    }

    /** @test */
    public function it_can_add_recipe_to_meal_slot()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();
        $recipe = Recipe::factory()->create();

        $response = $this->post(route('meal-plans.meals.update', $mealPlan), [
            'day' => 'monday',
            'meal_type' => 'dinner',
            'recipe_id' => $recipe->id,
            'action' => 'set'
        ]);

        $mealPlan->refresh();

        $this->assertEquals($recipe->id, $mealPlan->meals['monday']['dinner']);
    }

    /** @test */
    public function it_can_remove_recipe_from_meal_slot()
    {
        $recipe = Recipe::factory()->create();
        $mealPlan = WeeklyMealPlan::factory()->create([
            'meals' => [
                'monday' => [
                    'dinner' => $recipe->id
                ]
            ]
        ]);

        $response = $this->post(route('meal-plans.meals.update', $mealPlan), [
            'day' => 'monday',
            'meal_type' => 'dinner',
            'action' => 'remove'
        ]);

        $mealPlan->refresh();

        $this->assertArrayNotHasKey('dinner', $mealPlan->meals['monday'] ?? []);
    }

    /** @test */
    public function it_can_change_recipe_in_meal_slot()
    {
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        $mealPlan = WeeklyMealPlan::factory()->create([
            'meals' => [
                'tuesday' => [
                    'lunch' => $recipe1->id
                ]
            ]
        ]);

        $response = $this->post(route('meal-plans.meals.update', $mealPlan), [
            'day' => 'tuesday',
            'meal_type' => 'lunch',
            'recipe_id' => $recipe2->id,
            'action' => 'set'
        ]);

        $mealPlan->refresh();

        $this->assertEquals($recipe2->id, $mealPlan->meals['tuesday']['lunch']);
    }

    /** @test */
    public function it_validates_day_when_updating_meal()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();
        $recipe = Recipe::factory()->create();

        $response = $this->post(route('meal-plans.meals.update', $mealPlan), [
            'day' => 'invalid_day',
            'meal_type' => 'dinner',
            'recipe_id' => $recipe->id,
            'action' => 'set'
        ]);

        $response->assertSessionHasErrors('day');
    }

    /** @test */
    public function it_validates_meal_type_when_updating_meal()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();
        $recipe = Recipe::factory()->create();

        $response = $this->post(route('meal-plans.meals.update', $mealPlan), [
            'day' => 'monday',
            'meal_type' => 'invalid_meal',
            'recipe_id' => $recipe->id,
            'action' => 'set'
        ]);

        $response->assertSessionHasErrors('meal_type');
    }

    /** @test */
    public function it_calculates_week_end_correctly()
    {
        $weekStart = Carbon::parse('2025-01-06'); // Monday
        $mealPlan = WeeklyMealPlan::factory()->create([
            'week_start' => $weekStart
        ]);

        $expectedWeekEnd = Carbon::parse('2025-01-12'); // Sunday

        $this->assertTrue($mealPlan->week_end->isSameDay($expectedWeekEnd));
    }

    /** @test */
    public function it_returns_meal_for_specific_day()
    {
        $recipe = Recipe::factory()->create();
        $mealPlan = WeeklyMealPlan::factory()->create([
            'meals' => [
                'wednesday' => [
                    'breakfast' => $recipe->id
                ]
            ]
        ]);

        $meal = $mealPlan->getMealForDay('wednesday', 'breakfast');

        $this->assertNotNull($meal);
        $this->assertEquals($recipe->id, $meal->id);
    }

    /** @test */
    public function it_returns_null_for_empty_meal_slot()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();

        $meal = $mealPlan->getMealForDay('thursday', 'lunch');

        $this->assertNull($meal);
    }

    /** @test */
    public function it_gets_all_unique_recipes_from_meal_plan()
    {
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();
        $recipe3 = Recipe::factory()->create();

        $mealPlan = WeeklyMealPlan::factory()->create([
            'meals' => [
                'monday' => [
                    'breakfast' => $recipe1->id,
                    'dinner' => $recipe2->id
                ],
                'tuesday' => [
                    'lunch' => $recipe1->id, // Duplicate
                    'dinner' => $recipe3->id
                ]
            ]
        ]);

        $recipes = $mealPlan->getAllRecipes();

        $this->assertCount(3, $recipes); // Should only return unique recipes
        $this->assertTrue($recipes->contains('id', $recipe1->id));
        $this->assertTrue($recipes->contains('id', $recipe2->id));
        $this->assertTrue($recipes->contains('id', $recipe3->id));
    }

    /** @test */
    public function it_gets_all_recipes_including_duplicates()
    {
        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        $mealPlan = WeeklyMealPlan::factory()->create([
            'meals' => [
                'monday' => [
                    'lunch' => $recipe1->id,
                    'dinner' => $recipe2->id
                ],
                'friday' => [
                    'lunch' => $recipe1->id, // Same recipe again
                ]
            ]
        ]);

        $recipes = $mealPlan->getAllRecipesWithDuplicates();

        $this->assertCount(3, $recipes); // Should include duplicates
    }

    /** @test */
    public function it_handles_legacy_array_format_for_meals()
    {
        $recipe1 = Recipe::factory()->create();

        // Legacy format: array of recipe IDs instead of single ID
        $mealPlan = WeeklyMealPlan::factory()->create([
            'meals' => [
                'saturday' => [
                    'dinner' => [$recipe1->id] // Old array format
                ]
            ]
        ]);

        $meal = $mealPlan->getMealForDay('saturday', 'dinner');

        $this->assertNotNull($meal);
        $this->assertEquals($recipe1->id, $meal->id);
    }

    /** @test */
    public function it_returns_htmx_partial_when_updating_meal_via_htmx()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();
        $recipe = Recipe::factory()->create();

        $response = $this->post(
            route('meal-plans.meals.update', $mealPlan),
            [
                'day' => 'sunday',
                'meal_type' => 'breakfast',
                'recipe_id' => $recipe->id,
                'action' => 'set'
            ],
            ['HX-Request' => 'true']
        );

        $response->assertStatus(200);
        // Should return HTML with multiple hx-swap-oob targets
        $this->assertStringContainsString('hx-swap-oob', $response->getContent());
    }

    /** @test */
    public function it_handles_show_select_action_via_htmx()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();

        $response = $this->get(
            route('meal-plans.meals.show', $mealPlan) . '?day=monday&meal_type=breakfast&action=show_select',
            ['HX-Request' => 'true']
        );

        $response->assertStatus(200);
        $response->assertViewIs('meal-plans.partials.meal-slot-selecting');
    }

    /** @test */
    public function it_handles_cancel_select_action_via_htmx()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();

        $response = $this->get(
            route('meal-plans.meals.show', $mealPlan) . '?day=monday&meal_type=lunch&action=cancel_select',
            ['HX-Request' => 'true']
        );

        $response->assertStatus(200);
        $response->assertViewIs('meal-plans.partials.meal-slot');
    }

    /** @test */
    public function meal_plan_can_have_active_flag()
    {
        $activePlan = WeeklyMealPlan::factory()->create(['is_active' => true]);
        $inactivePlan = WeeklyMealPlan::factory()->create(['is_active' => false]);

        $this->assertTrue($activePlan->is_active);
        $this->assertFalse($inactivePlan->is_active);
    }

    /** @test */
    public function recipe_select_dropdown_shows_placeholder_as_selected()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();
        Recipe::factory()->count(3)->create();

        $response = $this->get(
            route('meal-plans.meals.show', $mealPlan) . '?day=monday&meal_type=breakfast&action=show_select',
            ['HX-Request' => 'true']
        );

        $response->assertStatus(200);
        // Verify placeholder option has both disabled and selected attributes
        $response->assertSee('disabled selected>Select recipe...</option>', false);
    }

    /** @test */
    public function can_select_first_recipe_in_list()
    {
        $mealPlan = WeeklyMealPlan::factory()->create();
        // Create recipes - first one alphabetically or by ID should be selectable
        $firstRecipe = Recipe::factory()->create(['title' => 'AAA First Recipe']);
        Recipe::factory()->create(['title' => 'BBB Second Recipe']);

        $response = $this->post(route('meal-plans.meals.update', $mealPlan), [
            'day' => 'monday',
            'meal_type' => 'breakfast',
            'recipe_id' => $firstRecipe->id,
            'action' => 'set'
        ]);

        $mealPlan->refresh();

        // First recipe in list should be selectable
        $this->assertEquals($firstRecipe->id, $mealPlan->meals['monday']['breakfast']);
    }
}
