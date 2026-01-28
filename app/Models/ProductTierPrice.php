<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTierPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'minimum_qty',
        'price',
        'is_active',
    ];

    protected $casts = [
        'minimum_qty' => 'integer',
        'price' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Scope active tier prices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}