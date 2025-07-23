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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // g, ml, cup, tbsp, etc.
            $table->string('name'); // Gram, Milliliter, Cup, Tablespoon, etc.
            $table->enum('type', ['mass', 'volume', 'count']); // Unit category
            $table->foreignId('base_unit_id')->nullable()->constrained('units')->onDelete('set null'); // Reference to base unit (g for mass, ml for volume)
            $table->decimal('factor', 12, 6)->default(1); // Conversion factor to base unit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
