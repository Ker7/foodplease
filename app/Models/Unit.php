<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Unit extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'type',
        'base_unit_id',
        'factor'
    ];

    protected $casts = [
        'factor' => 'decimal:6'
    ];

    /**
     * Base unit for this unit's type (g for mass, ml for volume)
     */
    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * Units that use this unit as their base
     */
    public function derivedUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    /**
     * Ingredients that have overrides for this unit
     */
    public function ingredientOverrides(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_unit_overrides')
                    ->withPivot('factor')
                    ->withTimestamps();
    }

    /**
     * Ingredients that use this as their default unit
     */
    public function ingredientsAsDefault(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'default_unit_id');
    }

    /**
     * Check if this unit is a base unit (has no parent)
     */
    public function isBaseUnit(): bool
    {
        return $this->base_unit_id === null;
    }

    /**
     * Get the chain of conversion factors to the base unit
     */
    public function getConversionChainToBase(): array
    {
        $chain = [];
        $currentUnit = $this;
        
        while (!$currentUnit->isBaseUnit()) {
            $chain[] = [
                'unit' => $currentUnit,
                'factor' => $currentUnit->factor
            ];
            $currentUnit = $currentUnit->baseUnit;
        }
        
        // Add the base unit
        $chain[] = [
            'unit' => $currentUnit,
            'factor' => 1
        ];
        
        return $chain;
    }

    /**
     * Calculate total conversion factor to base unit
     */
    public function getTotalFactorToBase(): float
    {
        if ($this->isBaseUnit()) {
            return 1;
        }
        
        $totalFactor = 1;
        $currentUnit = $this;
        
        while (!$currentUnit->isBaseUnit()) {
            $totalFactor *= $currentUnit->factor;
            $currentUnit = $currentUnit->baseUnit;
        }
        
        return $totalFactor;
    }

    /**
     * Get all units of the same type (mass, volume, count)
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get base units only
     */
    public function scopeBaseUnits($query)
    {
        return $query->whereNull('base_unit_id');
    }
}
