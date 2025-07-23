<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'default_unit_id'
    ];

    /**
     * Default unit for this ingredient
     */
    public function defaultUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'default_unit_id');
    }

    /**
     * Recipes that use this ingredient
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')
                    ->withPivot('amount', 'unit', 'unit_id', 'canonical_amount')
                    ->withTimestamps();
    }

    /**
     * Unit overrides for this ingredient
     */
    public function unitOverrides(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'ingredient_unit_overrides')
                    ->withPivot('factor')
                    ->withTimestamps();
    }

    /**
     * Get formatted amount for display (contextual with pivot data)
     */
    public function getFormattedAmountAttribute(): string
    {
        if (!$this->pivot) {
            return '';
        }

        $amount = $this->pivot->amount ?? $this->pivot->canonical_amount ?? '';
        $unit = $this->pivot->unit_id ? Unit::find($this->pivot->unit_id) : null;
        
        if ($amount && $unit) {
            $formattedAmount = $amount == floor($amount) ? number_format($amount, 0) : number_format($amount, 2);
            return trim($formattedAmount . ' ' . $unit->slug);
        }
        
        if ($amount) {
            return number_format($amount, 2);
        }
        
        return '';
    }

    /**
     * Add or update unit override for this ingredient
     */
    public function setUnitOverride(Unit $unit, float $factor): void
    {
        $this->unitOverrides()->syncWithoutDetaching([
            $unit->id => ['factor' => $factor]
        ]);
    }

    /**
     * Get unit override factor if exists
     */
    public function getUnitOverride(Unit $unit): ?float
    {
        $override = $this->unitOverrides()->where('unit_id', $unit->id)->first();
        return $override ? $override->pivot->factor : null;
    }
}
