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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['fridge', 'pantry', 'freezer']);
            $table->decimal('quantity', 8, 2)->default(0);
            $table->string('unit')->nullable();
            $table->string('location')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('low_stock_threshold', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
