<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'sku',
        'barcode',
        'name',
        'slug',
        'category_id',
        'description',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'unit',
        'images',
        'is_active',
        'is_featured',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_price' => 'integer',
        'selling_price' => 'integer',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Boot function
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            if (empty($model->sku)) {
                $model->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Relasi: Product belongs to Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Product punya banyak Stock Logs
     */
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    /**
     * Relasi: Product punya banyak Transaction Items
     */
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Relasi: User yang create product
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi: User yang update product
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Filter product aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter product featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Filter stock rendah
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    /**
     * Scope: Search by name, SKU, or barcode
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('sku', 'like', "%{$keyword}%")
              ->orWhere('barcode', 'like', "%{$keyword}%");
        });
    }

    /**
     * Check apakah stock rendah
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Get profit margin
     */
    public function getProfitMargin(): float
    {
        if ($this->purchase_price == 0) return 0;
        
        return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    /**
     * Get formatted price
     */
    public function getFormattedSellingPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    /**
     * Get first image URL
     */
    public function getFirstImageAttribute(): ?string
    {
        if (empty($this->images)) return null;
        return $this->images[0] ?? null;
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
 * Relasi ke Tier Prices
 */
public function tierPrices()
{
    return $this->hasMany(ProductTierPrice::class)->active()->orderBy('minimum_qty');
}

/**
 * Relasi ke Vendors melalui ProductVendorPrice
 */
public function vendors()
{
    return $this->belongsToMany(Vendor::class, 'product_vendor_prices')
        ->withPivot('purchase_price', 'effective_from', 'effective_to', 'notes')
        ->withTimestamps();
}

/**
 * Relasi ke ProductVendorPrice
 */
public function vendorPrices()
{
    return $this->hasMany(ProductVendorPrice::class);
}

/**
 * Get harga berdasarkan quantity (tier pricing)
 * 
 * @param int $quantity
 * @return int
 */
public function getPriceByQuantity(int $quantity): int
{
    // Ambil tier price yang sesuai
    $tierPrice = $this->tierPrices()
        ->where('minimum_qty', '<=', $quantity)
        ->orderBy('minimum_qty', 'desc')
        ->first();

    // Jika ada tier price, pakai harga tier
    if ($tierPrice) {
        return $tierPrice->price;
    }

    // Jika tidak ada, pakai harga normal
    return $this->selling_price;
}

/**
 * Check if product has tier pricing
 */
public function hasTierPricing(): bool
{
    return $this->tierPrices()->exists();
}

/**
 * Get cheapest vendor untuk produk ini dalam range tanggal
 */
public function getCheapestVendor($startDate = null, $endDate = null)
{
    $query = $this->vendorPrices()->with('vendor');

    if ($startDate && $endDate) {
        $query->inDateRange($startDate, $endDate);
    } else {
        $query->active();
    }

    return $query->orderBy('purchase_price', 'asc')->first();
}

/**
 * Get average vendor price dalam range tanggal
 */
public function getAverageVendorPrice($startDate = null, $endDate = null)
{
    $query = $this->vendorPrices();

    if ($startDate && $endDate) {
        $query->inDateRange($startDate, $endDate);
    } else {
        $query->active();
    }

    return $query->avg('purchase_price') ?? 0;
}
}