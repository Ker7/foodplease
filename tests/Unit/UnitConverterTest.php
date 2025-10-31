<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UnitConverter;
use App\Models\Unit;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnitConverterTest extends TestCase
{
    use RefreshDatabase;

    protected UnitConverter $converter;
    protected Unit $gram;
    protected Unit $kilogram;
    protected Unit $milliliter;
    protected Unit $liter;
    protected Unit $cup;
    protected Unit $tablespoon;
    protected Unit $teaspoon;
    protected Unit $piece;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new UnitConverter();

        // Seed units for testing
        $this->seed(\Database\Seeders\UnitSeeder::class);

        // Load commonly used units
        $this->gram = Unit::where('slug', 'g')->first();
        $this->kilogram = Unit::where('slug', 'kg')->first();
        $this->milliliter = Unit::where('slug', 'ml')->first();
        $this->liter = Unit::where('slug', 'l')->first();
        $this->cup = Unit::where('slug', 'cup')->first();
        $this->tablespoon = Unit::where('slug', 'tbsp')->first();
        $this->teaspoon = Unit::where('slug', 'tsp')->first();
        $this->piece = Unit::where('slug', 'piece')->first();
    }

    /** @test */
    public function it_converts_between_same_unit()
    {
        $result = $this->converter->convert(5, $this->gram, $this->gram);
        $this->assertEquals(5, $result);
    }

    /** @test */
    public function it_converts_grams_to_kilograms()
    {
        $result = $this->converter->convert(1000, $this->gram, $this->kilogram);
        $this->assertEquals(1, $result);
    }

    /** @test */
    public function it_converts_kilograms_to_grams()
    {
        $result = $this->converter->convert(2, $this->kilogram, $this->gram);
        $this->assertEquals(2000, $result);
    }

    /** @test */
    public function it_converts_milliliters_to_liters()
    {
        $result = $this->converter->convert(1000, $this->milliliter, $this->liter);
        $this->assertEquals(1, $result);
    }

    /** @test */
    public function it_converts_liters_to_milliliters()
    {
        $result = $this->converter->convert(1.5, $this->liter, $this->milliliter);
        $this->assertEquals(1500, $result);
    }

    /** @test */
    public function it_converts_cups_to_milliliters()
    {
        // 1 cup = 240ml
        $result = $this->converter->convert(1, $this->cup, $this->milliliter);
        $this->assertEquals(240, $result);
    }

    /** @test */
    public function it_converts_tablespoons_to_milliliters()
    {
        // 1 tbsp = 15ml
        $result = $this->converter->convert(2, $this->tablespoon, $this->milliliter);
        $this->assertEquals(30, $result);
    }

    /** @test */
    public function it_converts_teaspoons_to_tablespoons()
    {
        // 1 tbsp = 3 tsp
        $result = $this->converter->convert(6, $this->teaspoon, $this->tablespoon);
        $this->assertEquals(2, $result);
    }

    /** @test */
    public function it_converts_cups_to_tablespoons()
    {
        // 1 cup = 240ml, 1 tbsp = 15ml => 1 cup = 16 tbsp
        $result = $this->converter->convert(1, $this->cup, $this->tablespoon);
        $this->assertEquals(16, $result);
    }

    /** @test */
    public function it_throws_exception_for_incompatible_unit_types()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot convert between different unit types');

        $this->converter->convert(100, $this->gram, $this->milliliter);
    }

    /** @test */
    public function it_converts_to_canonical_unit_for_mass()
    {
        // Canonical unit for mass is gram
        $result = $this->converter->convertToCanonical(2, $this->kilogram);
        $this->assertEquals(2000, $result);
    }

    /** @test */
    public function it_converts_to_canonical_unit_for_volume()
    {
        // Canonical unit for volume is milliliter
        $result = $this->converter->convertToCanonical(2, $this->cup);
        $this->assertEquals(480, $result); // 2 cups = 480ml
    }

    /** @test */
    public function it_converts_from_canonical_unit_for_mass()
    {
        // Convert 1000g to kg
        $result = $this->converter->convertFromCanonical(1000, $this->kilogram);
        $this->assertEquals(1, $result);
    }

    /** @test */
    public function it_converts_from_canonical_unit_for_volume()
    {
        // Convert 240ml to cups
        $result = $this->converter->convertFromCanonical(240, $this->cup);
        $this->assertEquals(1, $result);
    }

    /** @test */
    public function it_handles_ingredient_specific_overrides()
    {
        // Create a test ingredient
        $flour = Ingredient::create([
            'name' => 'All-Purpose Flour',
            'default_unit_id' => $this->cup->id
        ]);

        // Add an override: 1 cup flour = 120g (instead of volume conversion)
        \DB::table('ingredient_unit_overrides')->insert([
            'ingredient_id' => $flour->id,
            'unit_id' => $this->cup->id,
            'factor' => 120, // 1 cup = 120g
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Convert 2 cups of flour to grams using override
        $result = $this->converter->convert(2, $this->cup, $this->gram, $flour);
        $this->assertEquals(240, $result); // 2 cups * 120g = 240g
    }

    /** @test */
    public function it_formats_amount_with_unit()
    {
        $result = $this->converter->formatAmount(2.5, $this->kilogram);
        $this->assertEquals('2.50 kg', $result);

        $result = $this->converter->formatAmount(3, $this->cup);
        $this->assertEquals('3 cup', $result);
    }

    /** @test */
    public function it_gets_compatible_units_for_type()
    {
        $massUnits = $this->converter->getCompatibleUnits('mass');

        $this->assertGreaterThan(0, $massUnits->count());
        $this->assertTrue($massUnits->contains('slug', 'g'));
        $this->assertTrue($massUnits->contains('slug', 'kg'));
        $this->assertFalse($massUnits->contains('slug', 'ml')); // ml is volume, not mass
    }

    /** @test */
    public function it_gets_base_unit_for_type()
    {
        $baseUnit = $this->converter->getBaseUnitForType('mass');
        $this->assertEquals('g', $baseUnit->slug);

        $baseUnit = $this->converter->getBaseUnitForType('volume');
        $this->assertEquals('ml', $baseUnit->slug);

        $baseUnit = $this->converter->getBaseUnitForType('count');
        $this->assertEquals('piece', $baseUnit->slug);
    }

    /** @test */
    public function it_checks_unit_compatibility()
    {
        $this->assertTrue($this->converter->areUnitsCompatible($this->gram, $this->kilogram));
        $this->assertTrue($this->converter->areUnitsCompatible($this->milliliter, $this->cup));
        $this->assertFalse($this->converter->areUnitsCompatible($this->gram, $this->milliliter));
    }

    /** @test */
    public function it_throws_exception_for_invalid_unit_id()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unit not found');

        $this->converter->convert(5, 99999, $this->gram);
    }

    /** @test */
    public function it_handles_decimal_conversions_accurately()
    {
        // Test precision with decimal amounts
        $result = $this->converter->convert(0.5, $this->cup, $this->milliliter);
        $this->assertEquals(120, $result); // 0.5 cup = 120ml

        $result = $this->converter->convert(1.5, $this->tablespoon, $this->teaspoon);
        $this->assertEquals(4.5, $result); // 1.5 tbsp = 4.5 tsp
    }

    /** @test */
    public function it_handles_complex_conversion_chains()
    {
        // Test converting through multiple levels (tsp -> tbsp -> cup -> ml -> L)
        $result = $this->converter->convert(48, $this->teaspoon, $this->cup);
        // 48 tsp = 16 tbsp = 1 cup
        $this->assertEquals(1, $result);
    }

    /** @test */
    public function it_handles_piece_unit_as_base_count_unit()
    {
        $result = $this->converter->convertToCanonical(5, $this->piece);
        $this->assertEquals(5, $result); // piece is already canonical for count
    }

    /** @test */
    public function it_converts_with_unit_ids_instead_of_models()
    {
        // Test that we can pass unit IDs instead of models
        $result = $this->converter->convert(1000, $this->gram->id, $this->kilogram->id);
        $this->assertEquals(1, $result);
    }

    /** @test */
    public function it_gets_suggested_units_for_ingredient_with_default_unit()
    {
        $flour = Ingredient::create([
            'name' => 'Flour',
            'default_unit_id' => $this->cup->id
        ]);

        $suggestedUnits = $this->converter->getSuggestedUnitsForIngredient($flour);

        // Should return volume units since cup is volume
        $this->assertTrue($suggestedUnits->contains('slug', 'cup'));
        $this->assertTrue($suggestedUnits->contains('slug', 'ml'));
        $this->assertTrue($suggestedUnits->contains('slug', 'l'));
    }

    /** @test */
    public function it_aggregates_ingredients_correctly_across_recipes()
    {
        // Simulate shopping list aggregation scenario
        // Recipe 1: 2 cups flour
        // Recipe 2: 500g flour
        // Result should aggregate in canonical unit (grams)

        $flour = Ingredient::create([
            'name' => 'Flour',
            'default_unit_id' => $this->cup->id
        ]);

        // Add override for flour: 1 cup = 120g
        \DB::table('ingredient_unit_overrides')->insert([
            'ingredient_id' => $flour->id,
            'unit_id' => $this->cup->id,
            'factor' => 120,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Convert both to canonical (grams)
        $amount1 = $this->converter->convertToCanonical(2, $this->cup, $flour); // 2 cups = 240g
        $amount2 = $this->converter->convertToCanonical(500, $this->gram, $flour); // 500g

        $totalGrams = $amount1 + $amount2; // 740g total

        $this->assertEquals(740, $totalGrams);

        // Convert back to display unit (cups)
        $displayAmount = $this->converter->convertFromCanonical($totalGrams, $this->cup, $flour);
        $this->assertEquals(6.166666666667, round($displayAmount, 12)); // ~6.17 cups
    }
}
