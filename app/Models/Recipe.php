<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'source_url',
        'prep_time',
        'cook_time',
        'servings',
        'instructions',
        'notes'
    ];

    protected $casts = [
        'instructions' => 'array'
    ];

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
                    ->withPivot('amount', 'unit')
                    ->withTimestamps();
    }

    public function getTotalTimeAttribute(): ?int
    {
        if ($this->prep_time && $this->cook_time) {
            return $this->prep_time + $this->cook_time;
        }
        return $this->prep_time ?? $this->cook_time;
    }
}
