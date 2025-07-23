<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type',
        'color'
    ];

    public function scopeForRecipes($query)
    {
        return $query->where('type', 'recipe');
    }

    public function scopeForInventory($query)
    {
        return $query->where('type', 'inventory');
    }

    public function scopeForMealPlans($query)
    {
        return $query->where('type', 'meal_plan');
    }
}
