<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class UnitConverter
{
    /**
     * Convert amount from one unit to another
     * 
     * @param float $amount Amount to convert
     * @param Unit|int $fromUnit Source unit (model or ID)
     * @param Unit|int $toUnit Target unit (model or ID)
     * @param Ingredient|int|null $ingredient Ingredient for override lookup (model or ID)
     * @return float Converted amount
     * @throws \Exception If conversion is not possible
     */
    public function convert(float $amount, $fromUnit, $toUnit, $ingredient = null): float
    {
        // Handle unit loading
        $fromUnit = $this->loadUnit($fromUnit);
        $toUnit = $this->loadUnit($toUnit);

        // Same unit, no conversion needed
        if ($fromUnit->id === $toUnit->id) {
            return $amount;
        }

        $ingredientId = $ingredient ? ($ingredient instanceof Ingredient ? $ingredient->id : $ingredient) : null;

        // Check if ingredient overrides exist for cross-type conversions
        $hasFromOverride = $ingredientId && $this->getIngredientOverrideFactor($fromUnit, $ingredientId) !== null;
        $hasToOverride = $ingredientId && $this->getIngredientOverrideFactor($toUnit, $ingredientId) !== null;

        // Check if units are of compatible types (unless ingredient overrides allow cross-type conversion)
        if ($fromUnit->type !== $toUnit->type && !($hasFromOverride || $hasToOverride)) {
            throw new \Exception("Cannot convert between different unit types: {$fromUnit->type} to {$toUnit->type}");
        }

        // Convert from source unit to base unit
        $baseAmount = $this->convertToBase($amount, $fromUnit, $ingredientId);

        // Convert from base unit to target unit
        return $this->convertFromBase($baseAmount, $toUnit, $ingredientId);
    }
    
    /**
     * Convert amount to canonical base unit (grams for mass, ml for volume)
     */
    public function convertToCanonical(float $amount, $unit, $ingredient = null): float
    {
        $unit = $this->loadUnit($unit);
        $baseUnit = $this->getBaseUnitForType($unit->type);
        
        return $this->convert($amount, $unit, $baseUnit, $ingredient);
    }
    
    /**
     * Convert amount from canonical base unit to display unit
     */
    public function convertFromCanonical(float $canonicalAmount, $targetUnit, $ingredient = null): float
    {
        $targetUnit = $this->loadUnit($targetUnit);
        $baseUnit = $this->getBaseUnitForType($targetUnit->type);
        
        return $this->convert($canonicalAmount, $baseUnit, $targetUnit, $ingredient);
    }
    
    /**
     * Get formatted display string for amount and unit
     */
    public function formatAmount(float $amount, $unit, $ingredient = null): string
    {
        $unit = $this->loadUnit($unit);
        
        // Format number appropriately
        $formattedAmount = $amount == floor($amount) ? number_format($amount, 0) : number_format($amount, 2);
        
        return trim($formattedAmount . ' ' . $unit->slug);
    }
    
    /**
     * Get all compatible units for a given unit type
     */
    public function getCompatibleUnits(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return Unit::ofType($type)->orderBy('name')->get();
    }
    
    /**
     * Get ingredient-specific override factor if it exists
     */
    protected function getIngredientOverrideFactor(Unit $unit, ?int $ingredientId): ?float
    {
        if (!$ingredientId) {
            return null;
        }
        
        $override = DB::table('ingredient_unit_overrides')
            ->where('ingredient_id', $ingredientId)
            ->where('unit_id', $unit->id)
            ->first();
            
        return $override ? (float) $override->factor : null;
    }
    
    /**
     * Convert amount from unit to its base unit
     */
    protected function convertToBase(float $amount, Unit $unit, ?int $ingredientId = null): float
    {
        if ($unit->isBaseUnit()) {
            return $amount;
        }
        
        // Check for ingredient-specific override
        $overrideFactor = $this->getIngredientOverrideFactor($unit, $ingredientId);
        if ($overrideFactor !== null) {
            return $amount * $overrideFactor;
        }
        
        // Use standard conversion chain
        return $amount * $unit->getTotalFactorToBase();
    }
    
    /**
     * Convert amount from base unit to target unit
     */
    protected function convertFromBase(float $baseAmount, Unit $targetUnit, ?int $ingredientId = null): float
    {
        if ($targetUnit->isBaseUnit()) {
            return $baseAmount;
        }
        
        // Check for ingredient-specific override
        $overrideFactor = $this->getIngredientOverrideFactor($targetUnit, $ingredientId);
        if ($overrideFactor !== null) {
            return $baseAmount / $overrideFactor;
        }
        
        // Use standard conversion chain
        return $baseAmount / $targetUnit->getTotalFactorToBase();
    }
    
    /**
     * Load unit model from ID or return existing model
     */
    protected function loadUnit($unit): Unit
    {
        if ($unit instanceof Unit) {
            return $unit;
        }
        
        $loadedUnit = Unit::find($unit);
        if (!$loadedUnit) {
            throw new \Exception("Unit not found: {$unit}");
        }
        
        return $loadedUnit;
    }
    
    /**
     * Get the base unit for a given type
     */
    public function getBaseUnitForType(string $type): Unit
    {
        $baseUnit = Unit::ofType($type)->baseUnits()->first();
        
        if (!$baseUnit) {
            throw new \Exception("No base unit found for type: {$type}");
        }
        
        return $baseUnit;
    }
    
    /**
     * Check if two units are compatible (same type)
     */
    public function areUnitsCompatible($unit1, $unit2): bool
    {
        $unit1 = $this->loadUnit($unit1);
        $unit2 = $this->loadUnit($unit2);
        
        return $unit1->type === $unit2->type;
    }
    
    /**
     * Get suggested units for an ingredient based on its type and common usage
     */
    public function getSuggestedUnitsForIngredient(Ingredient $ingredient): \Illuminate\Database\Eloquent\Collection
    {
        // If ingredient has a default unit, prioritize units of that type
        if ($ingredient->defaultUnit) {
            return $this->getCompatibleUnits($ingredient->defaultUnit->type);
        }
        
        // Otherwise return all units, but prioritize common ones
        return Unit::orderByRaw("
            CASE 
                WHEN slug IN ('g', 'ml', 'piece', 'cup', 'tbsp', 'tsp') THEN 1
                ELSE 2
            END,
            name
        ")->get();
    }
}