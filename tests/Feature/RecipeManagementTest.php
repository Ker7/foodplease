<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecipeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Unit $cup;
    protected Unit $gram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\UnitSeeder::class);

        $this->cup = Unit::where('slug', 'cup')->first();
        $this->gram = Unit::where('slug', 'g')->first();
    }

    /** @test */
    public function it_can_view_recipe_index()
    {
        Recipe::factory()->count(3)->create();

        $response = $this->get(route('recipes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('recipes.index');
        $response->assertViewHas('recipes');
    }

    /** @test */
    public function it_can_view_recipe_index_as_htmx_request()
    {
        Recipe::factory()->count(3)->create();

        $response = $this->get(route('recipes.index'), ['HX-Request' => 'true']);

        $response->assertStatus(200);
        $response->assertViewIs('recipes.partials.list');
    }

    /** @test */
    public function it_can_create_a_recipe()
    {
        $recipeData = [
            'title' => 'Chocolate Chip Cookies',
            'source_url' => 'https://example.com/recipe',
            'prep_time' => 15,
            'cook_time' => 12,
            'servings' => 24,
            'instructions' => [
                'Preheat oven to 375°F',
                'Mix dry ingredients',
                'Add wet ingredients',
                'Bake for 12 minutes'
            ],
            'notes' => 'Best served warm'
        ];

        $response = $this->post(route('recipes.store'), $recipeData);

        $response->assertRedirect();
        $this->assertDatabaseHas('recipes', [
            'title' => 'Chocolate Chip Cookies',
            'prep_time' => 15,
            'cook_time' => 12,
            'servings' => 24
        ]);

        $recipe = Recipe::where('title', 'Chocolate Chip Cookies')->first();
        $this->assertEquals(4, count($recipe->instructions));
    }

    /** @test */
    public function it_can_create_recipe_via_htmx()
    {
        $recipeData = [
            'title' => 'Quick Pasta',
            'prep_time' => 5,
            'cook_time' => 10,
            'servings' => 2,
            'instructions' => ['Boil pasta', 'Add sauce']
        ];

        $response = $this->post(route('recipes.store'), $recipeData, ['HX-Request' => 'true']);

        $response->assertStatus(200);
        $response->assertViewIs('recipes.partials.card');
        $this->assertDatabaseHas('recipes', ['title' => 'Quick Pasta']);
    }

    /** @test */
    public function it_requires_title_when_creating_recipe()
    {
        $response = $this->post(route('recipes.store'), [
            'prep_time' => 10,
            'cook_time' => 20,
            'servings' => 4
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function it_can_view_a_single_recipe()
    {
        $recipe = Recipe::factory()->create();

        $response = $this->get(route('recipes.show', $recipe));

        $response->assertStatus(200);
        $response->assertViewIs('recipes.show');
        $response->assertViewHas('recipe');
        $response->assertSee($recipe->title);
    }

    /** @test */
    public function it_can_update_a_recipe()
    {
        $recipe = Recipe::factory()->create([
            'title' => 'Original Title',
            'prep_time' => 10
        ]);

        $response = $this->put(route('recipes.update', $recipe), [
            'title' => 'Updated Title',
            'prep_time' => 15,
            'cook_time' => 20,
            'servings' => 4,
            'instructions' => ['Step 1']
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'title' => 'Updated Title',
            'prep_time' => 15
        ]);
    }

    /** @test */
    public function it_can_delete_a_recipe()
    {
        $recipe = Recipe::factory()->create();

        $response = $this->delete(route('recipes.destroy', $recipe));

        $response->assertRedirect(route('recipes.index'));
        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
    }

    /** @test */
    public function it_can_add_ingredient_to_recipe()
    {
        $recipe = Recipe::factory()->create();
        $flour = Ingredient::create(['name' => 'Flour', 'default_unit_id' => $this->cup->id]);

        $response = $this->post(route('recipes.ingredients.store', $recipe), [
            'existing_ingredient_id' => $flour->id,
            'amount' => 2,
            'unit_id' => $this->cup->id
        ]);

        $this->assertDatabaseHas('recipe_ingredients', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $flour->id,
            'amount' => 2,
            'unit_id' => $this->cup->id
        ]);
    }

    /** @test */
    public function it_can_create_new_ingredient_when_adding_to_recipe()
    {
        $recipe = Recipe::factory()->create();

        $response = $this->post(route('recipes.ingredients.store', $recipe), [
            'name' => 'New Ingredient',
            'amount' => 1.5,
            'unit_id' => $this->gram->id
        ]);

        $this->assertDatabaseHas('ingredients', ['name' => 'New Ingredient']);

        $ingredient = Ingredient::where('name', 'New Ingredient')->first();
        $this->assertDatabaseHas('recipe_ingredients', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'amount' => 1.5
        ]);
    }

    /** @test */
    public function it_can_update_recipe_ingredient_amount()
    {
        $recipe = Recipe::factory()->create();
        $sugar = Ingredient::create(['name' => 'Sugar', 'default_unit_id' => $this->cup->id]);

        $recipe->ingredients()->attach($sugar->id, [
            'amount' => 1,
            'unit_id' => $this->cup->id
        ]);

        $response = $this->put(route('recipes.ingredients.update', [$recipe, $sugar]), [
            'existing_ingredient_id' => $sugar->id,
            'amount' => 2,
            'unit_id' => $this->cup->id
        ]);

        $this->assertDatabaseHas('recipe_ingredients', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $sugar->id,
            'amount' => 2
        ]);
    }

    /** @test */
    public function it_can_remove_ingredient_from_recipe()
    {
        $recipe = Recipe::factory()->create();
        $salt = Ingredient::create(['name' => 'Salt', 'default_unit_id' => $this->gram->id]);

        $recipe->ingredients()->attach($salt->id, [
            'amount' => 1,
            'unit_id' => $this->gram->id
        ]);

        $response = $this->delete(route('recipes.ingredients.destroy', [$recipe, $salt]));

        $this->assertDatabaseMissing('recipe_ingredients', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $salt->id
        ]);
    }

    /** @test */
    public function it_calculates_total_time_correctly()
    {
        $recipe = Recipe::factory()->create([
            'prep_time' => 15,
            'cook_time' => 30
        ]);

        $this->assertEquals(45, $recipe->total_time);
    }

    /** @test */
    public function it_handles_null_times_gracefully()
    {
        $recipe = Recipe::factory()->create([
            'prep_time' => null,
            'cook_time' => 20
        ]);

        $this->assertEquals(20, $recipe->total_time);
    }

    /** @test */
    public function recipe_instructions_are_stored_as_json_array()
    {
        $recipe = Recipe::factory()->create([
            'instructions' => [
                'Step 1: Mix ingredients',
                'Step 2: Bake at 350°F',
                'Step 3: Let cool'
            ]
        ]);

        $recipe->refresh();

        $this->assertIsArray($recipe->instructions);
        $this->assertCount(3, $recipe->instructions);
        $this->assertEquals('Step 1: Mix ingredients', $recipe->instructions[0]);
    }

    /** @test */
    public function it_loads_ingredients_with_recipe()
    {
        $recipe = Recipe::factory()->create();
        $flour = Ingredient::create(['name' => 'Flour', 'default_unit_id' => $this->cup->id]);
        $sugar = Ingredient::create(['name' => 'Sugar', 'default_unit_id' => $this->cup->id]);

        $recipe->ingredients()->attach($flour->id, ['amount' => 2, 'unit_id' => $this->cup->id]);
        $recipe->ingredients()->attach($sugar->id, ['amount' => 1, 'unit_id' => $this->cup->id]);

        $recipe->load('ingredients');

        $this->assertCount(2, $recipe->ingredients);
        $this->assertEquals(2, $recipe->ingredients->first()->pivot->amount);
    }
}
