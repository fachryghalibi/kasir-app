<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVendorPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor_id',
        'purchase_price',
        'effective_from',
        'effective_to',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_price' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Relasi ke User (created by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope untuk harga yang masih berlaku (active)
     */
    public function scopeActive($query)
    {
        return $query->where('effective_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            });
    }

    /**
     * Scope untuk harga dalam range tanggal tertentu
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('effective_from', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('effective_from', '<=', $startDate)
                     ->where(function($q3) use ($endDate) {
                         $q3->whereNull('effective_to')
                            ->orWhere('effective_to', '>=', $endDate);
                     });
              });
        });
    }

    /**
     * Check if price is currently active
     */
    public function isActive(): bool
    {
        $now = now()->toDateString();
        return $this->effective_from <= $now && 
               (is_null($this->effective_to) || $this->effective_to >= $now);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }
}