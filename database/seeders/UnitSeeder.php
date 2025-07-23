<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Base units (no parent, factor = 1)
        $gram = Unit::create([
            'slug' => 'g',
            'name' => 'Gram',
            'type' => 'mass',
            'base_unit_id' => null,
            'factor' => 1,
        ]);

        $milliliter = Unit::create([
            'slug' => 'ml',
            'name' => 'Milliliter',
            'type' => 'volume',
            'base_unit_id' => null,
            'factor' => 1,
        ]);

        $piece = Unit::create([
            'slug' => 'piece',
            'name' => 'Piece',
            'type' => 'count',
            'base_unit_id' => null,
            'factor' => 1,
        ]);

        // Mass units (all convert to grams)
        Unit::create([
            'slug' => 'kg',
            'name' => 'Kilogram',
            'type' => 'mass',
            'base_unit_id' => $gram->id,
            'factor' => 1000, // 1 kg = 1000g
        ]);

        Unit::create([
            'slug' => 'lb',
            'name' => 'Pound',
            'type' => 'mass',
            'base_unit_id' => $gram->id,
            'factor' => 453.592, // 1 lb = 453.592g
        ]);

        Unit::create([
            'slug' => 'oz',
            'name' => 'Ounce',
            'type' => 'mass',
            'base_unit_id' => $gram->id,
            'factor' => 28.3495, // 1 oz = 28.3495g
        ]);

        // Volume units (all convert to milliliters)
        Unit::create([
            'slug' => 'l',
            'name' => 'Liter',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 1000, // 1 l = 1000ml
        ]);

        Unit::create([
            'slug' => 'cup',
            'name' => 'Cup',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 240, // 1 cup = 240ml (US standard)
        ]);

        Unit::create([
            'slug' => 'tbsp',
            'name' => 'Tablespoon',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 15, // 1 tbsp = 15ml
        ]);

        Unit::create([
            'slug' => 'tsp',
            'name' => 'Teaspoon',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 5, // 1 tsp = 5ml
        ]);

        Unit::create([
            'slug' => 'fl_oz',
            'name' => 'Fluid Ounce',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 29.5735, // 1 fl oz = 29.5735ml (US)
        ]);

        Unit::create([
            'slug' => 'pt',
            'name' => 'Pint',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 473.176, // 1 pt = 473.176ml (US)
        ]);

        Unit::create([
            'slug' => 'qt',
            'name' => 'Quart',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 946.353, // 1 qt = 946.353ml (US)
        ]);

        Unit::create([
            'slug' => 'gal',
            'name' => 'Gallon',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 3785.41, // 1 gal = 3785.41ml (US)
        ]);

        // Count units
        Unit::create([
            'slug' => 'dozen',
            'name' => 'Dozen',
            'type' => 'count',
            'base_unit_id' => $piece->id,
            'factor' => 12, // 1 dozen = 12 pieces
        ]);

        Unit::create([
            'slug' => 'pair',
            'name' => 'Pair',
            'type' => 'count',
            'base_unit_id' => $piece->id,
            'factor' => 2, // 1 pair = 2 pieces
        ]);

        // Additional common units
        Unit::create([
            'slug' => 'clove',
            'name' => 'Clove',
            'type' => 'count',
            'base_unit_id' => $piece->id,
            'factor' => 1, // 1 clove = 1 piece (for garlic, etc.)
        ]);

        Unit::create([
            'slug' => 'head',
            'name' => 'Head',
            'type' => 'count',
            'base_unit_id' => $piece->id,
            'factor' => 1, // 1 head = 1 piece (for lettuce, cabbage, etc.)
        ]);

        Unit::create([
            'slug' => 'bunch',
            'name' => 'Bunch',
            'type' => 'count',
            'base_unit_id' => $piece->id,
            'factor' => 1, // 1 bunch = 1 piece (for herbs, etc.)
        ]);

        Unit::create([
            'slug' => 'pinch',
            'name' => 'Pinch',
            'type' => 'mass',
            'base_unit_id' => $gram->id,
            'factor' => 0.3, // 1 pinch ≈ 0.3g
        ]);

        Unit::create([
            'slug' => 'dash',
            'name' => 'Dash',
            'type' => 'volume',
            'base_unit_id' => $milliliter->id,
            'factor' => 0.6, // 1 dash ≈ 0.6ml
        ]);
    }
}