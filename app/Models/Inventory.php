<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Inventory extends Model
{
    protected $fillable = [
        'name',
        'category',
        'quantity',
        'unit',
        'unit_id',
        'location',
        'expiry_date',
        'low_stock_threshold'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'low_stock_threshold' => 'decimal:2'
    ];

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                    ->where('expiry_date', '>=', Carbon::now());
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'low_stock_threshold');
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->lte(Carbon::now()->addDays(7));
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
