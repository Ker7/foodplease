<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new standalone ingredients table
        Schema::create('standalone_ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('default_unit')->nullable();
            $table->timestamps();
        });

        // Create pivot table for recipe-ingredient relationships
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->references('id')->on('standalone_ingredients')->onDelete('cascade');
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();
            
            $table->unique(['recipe_id', 'ingredient_id']);
        });

        // Migrate existing data
        $existingIngredients = DB::table('ingredients')->get();
        $ingredientMap = [];

        foreach ($existingIngredients as $ingredient) {
            // Check if ingredient already exists
            if (!isset($ingredientMap[$ingredient->item])) {
                $standaloneId = DB::table('standalone_ingredients')->insertGetId([
                    'name' => $ingredient->item,
                    'default_unit' => $ingredient->unit,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $ingredientMap[$ingredient->item] = $standaloneId;
            }

            // Create recipe-ingredient relationship
            DB::table('recipe_ingredients')->insert([
                'recipe_id' => $ingredient->recipe_id,
                'ingredient_id' => $ingredientMap[$ingredient->item],
                'amount' => $ingredient->amount,
                'unit' => $ingredient->unit,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop old ingredients table
        Schema::dropIfExists('ingredients');

        // Rename standalone_ingredients to ingredients
        Schema::rename('standalone_ingredients', 'ingredients');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename ingredients back to standalone_ingredients
        Schema::rename('ingredients', 'standalone_ingredients');

        // Recreate old ingredients table
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->string('item');
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();
        });

        // Restore old data
        $recipeIngredients = DB::table('recipe_ingredients')
            ->join('standalone_ingredients', 'recipe_ingredients.ingredient_id', '=', 'standalone_ingredients.id')
            ->select('recipe_ingredients.*', 'standalone_ingredients.name')
            ->get();

        foreach ($recipeIngredients as $ri) {
            DB::table('ingredients')->insert([
                'recipe_id' => $ri->recipe_id,
                'item' => $ri->name,
                'amount' => $ri->amount,
                'unit' => $ri->unit,
                'created_at' => $ri->created_at,
                'updated_at' => $ri->updated_at,
            ]);
        }

        // Drop new tables
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('standalone_ingredients');
    }
};
