<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'contact_person',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->code)) {
                $model->code = 'VND-' . strtoupper(Str::random(6));
            }
        });
    }

    /**
     * Relasi ke Product melalui ProductVendorPrice
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_vendor_prices')
            ->withPivot('purchase_price', 'effective_from', 'effective_to', 'notes')
            ->withTimestamps();
    }

    /**
     * Relasi ke ProductVendorPrice
     */
    public function productPrices()
    {
        return $this->hasMany(ProductVendorPrice::class);
    }

    /**
     * Scope active vendors
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}