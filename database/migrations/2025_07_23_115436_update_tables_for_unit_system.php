<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update ingredients table to reference default unit
        Schema::table('ingredients', function (Blueprint $table) {
            $table->foreignId('default_unit_id')->nullable()->after('name')->constrained('units')->onDelete('set null');
            $table->dropColumn('default_unit'); // Remove old string-based unit
        });

        // Update inventories table to reference unit
        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('quantity')->constrained('units')->onDelete('set null');
            // Keep the old 'unit' column for now - we'll migrate data then drop it
        });

        // Update recipe_ingredients pivot to reference unit and store canonical amounts
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('amount')->constrained('units')->onDelete('set null');
            $table->decimal('canonical_amount', 12, 6)->nullable()->after('amount'); // Amount in base units (grams/ml)
            // Keep the old 'unit' column for now - we'll migrate data then drop it
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('default_unit')->nullable();
            $table->dropForeign(['default_unit_id']);
            $table->dropColumn('default_unit_id');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'canonical_amount']);
        });
    }
};
