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
     * Relasi ke User (creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke CashFlow (polymorphic)
     */
    public function cashFlow()
    {
        return $this->morphOne(CashFlow::class, 'reference');
    }

    /**
     * Create cash flow untuk pembelian stock dari vendor
     */
    public function createCashFlow()
    {
        // Cek apakah sudah ada cash flow
        if ($this->cashFlow) {
            return $this->cashFlow;
        }

        // Cari atau buat kategori "Pembelian Stock"
        $category = CashFlowCategory::firstOrCreate(
            [
                'name' => 'Pembelian Stock',
                'type' => 'expense',
            ],
            [
                'description' => 'Pengeluaran untuk pembelian stock produk dari vendor',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        // Calculate total expense
        $totalExpense = $this->quantity * $this->purchase_price;

        return CashFlow::create([
            'type' => 'expense',
            'source' => 'purchase',
            'cash_flow_category_id' => $category->id,
            'amount' => $totalExpense,
            'description' => sprintf(
                'Pembelian stock: %s (%d %s) dari %s @ Rp %s',
                $this->product->name,
                $this->quantity,
                $this->product->unit,
                $this->vendor->name,
                number_format($this->purchase_price, 0, ',', '.')
            ),
            'reference_type' => self::class,
            'reference_id' => $this->id,
            'transaction_date' => $this->effective_from,
            'payment_method' => null,
            'receipt_number' => null,
            'vendor_id' => $this->vendor_id,
            'created_by' => $this->created_by,
            'approval_status' => 'approved',
            'approved_by' => $this->created_by,
            'approved_at' => now(),
        ]);
    }

    /**
     * Scope: Active vendor prices (masih berlaku)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('effective_to')
            ->orWhere('effective_to', '>=', now());
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
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }

    /**
     * Get total value (quantity × price)
     */
    public function getTotalValueAttribute(): int
    {
        return $this->quantity * $this->purchase_price;
    }

    /**
     * Get formatted total value
     */
    public function getFormattedTotalValueAttribute(): string
    {
        return 'Rp ' . number_format($this->total_value, 0, ',', '.');
    }
}