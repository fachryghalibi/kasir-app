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
        'quantity',
        'effective_from',
        'effective_to',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_price' => 'integer',
        'quantity' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    /**
     * Boot function
     */
    protected static function boot()
    {
        parent::boot();

        // ✨ Auto-create cash flow saat pembelian dari vendor
        static::created(function ($model) {
            $model->createCashFlow();
        });
    }

    /**
     * Relasi: ProductVendorPrice belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi: ProductVendorPrice belongs to Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Relasi: User yang create
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ✨ Relasi ke CashFlow
     */
    public function cashFlow()
    {
        return $this->morphOne(CashFlow::class, 'reference');
    }

    /**
     * ✅ PERBAIKAN: Create cash flow otomatis untuk purchase
     */
    public function createCashFlow()
    {
        // Cek apakah sudah ada cash flow
        if ($this->cashFlow) {
            return $this->cashFlow;
        }

        // Total amount = purchase_price × quantity
        $totalAmount = $this->purchase_price * $this->quantity;

        return CashFlow::create([
            'type' => 'expense',
            'source' => 'purchase',
            'cash_flow_category_id' => null, // ✅ PERBAIKAN: Tambahkan field ini
            'amount' => $totalAmount,
            'description' => sprintf(
                'Pembelian %s - %d %s @ Rp %s dari %s',
                $this->product->name,
                $this->quantity,
                $this->product->unit,
                number_format($this->purchase_price, 0, ',', '.'),
                $this->vendor->name
            ),
            'reference_type' => self::class,
            'reference_id' => $this->id,
            'transaction_date' => $this->effective_from->format('Y-m-d'),
            'payment_method' => null, // ✅ PERBAIKAN: Tambahkan field ini
            'receipt_number' => null, // ✅ PERBAIKAN: Tambahkan field ini
            'vendor_id' => $this->vendor_id,
            'created_by' => $this->created_by,
            'approval_status' => 'approved',
            'approved_by' => $this->created_by, // ✅ PERBAIKAN: Tambahkan field ini
            'approved_at' => now(), // ✅ PERBAIKAN: Tambahkan field ini
        ]);
    }

    /**
     * Scope: Active prices (effective_to is null or in future)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('effective_to')
              ->orWhere('effective_to', '>=', now());
        });
    }

    /**
     * Scope: In date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where('effective_from', '<=', $endDate)
            ->where(function ($q) use ($startDate) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $startDate);
            });
    }

    /**
     * Scope: By vendor
     */
    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope: By product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Check if price is still active
     */
    public function isActive(): bool
    {
        if (!$this->effective_to) {
            return true;
        }
        
        return $this->effective_to->isFuture() || $this->effective_to->isToday();
    }

    /**
     * Get formatted purchase price
     */
    public function getFormattedPurchasePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }

    /**
     * Get total amount (price × quantity)
     */
    public function getTotalAmountAttribute(): int
    {
        return $this->purchase_price * $this->quantity;
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
}