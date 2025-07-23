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
        Schema::create('weekly_meal_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Weekly Plan');
            $table->date('week_start');
            $table->json('meals')->nullable(); // Store day->meal_type->recipe_id mapping
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_meal_plans');
    }
};
