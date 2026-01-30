<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'invoice_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount_amount',
        'discount_type',
        'discount_value',
        'tax_amount',
        'tax_rate',
        'total',
        'payment_method',
        'paid_amount',
        'change_amount',
        'status',
        'refund_reason',
        'refunded_by',
        'refunded_at',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'discount_value' => 'decimal:2',
        'tax_amount' => 'integer',
        'tax_rate' => 'decimal:2',
        'total' => 'integer',
        'paid_amount' => 'integer',
        'change_amount' => 'integer',
        'refunded_at' => 'datetime',
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
            if (empty($model->invoice_number)) {
                $model->invoice_number = static::generateInvoiceNumber();
            }
        });

        // ✨ AUTO CREATE CASH FLOW
        static::created(function ($model) {
            if ($model->status === 'completed') {
                $model->createCashFlow();
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty('status')) {
                if ($model->status === 'completed' && !$model->cashFlow) {
                    $model->createCashFlow();
                } elseif ($model->status === 'refunded') {
                    $model->createRefundCashFlow();
                }
            }
        });
    }

    /**
     * Generate Invoice Number
     * Format: INV-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $lastInvoice = static::whereDate('created_at', now())
            ->latest('id')
            ->first();
        
        $sequence = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        return sprintf('INV-%s-%04d', $date, $sequence);
    }

    /**
     * Relasi: Transaction belongs to User (Kasir)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Transaction punya banyak Transaction Items
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Relasi: User yang melakukan refund
     */
    public function refunder()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    /**
     * ✨ Relasi ke CashFlow
     */
    public function cashFlow()
    {
        return $this->morphOne(CashFlow::class, 'reference');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Today's transactions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    /**
     * Scope: This month's transactions
     */
    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
                     ->whereMonth('created_at', now()->month);
    }

    /**
     * Scope: Date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: By payment method
     */
    public function scopePaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Calculate totals
     */
    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('subtotal');
        
        // Calculate discount
        if ($this->discount_type === 'percentage') {
            $this->discount_amount = ($this->subtotal * $this->discount_value) / 100;
        } else {
            $this->discount_amount = $this->discount_value ?? 0;
        }
        
        // Calculate tax
        $afterDiscount = $this->subtotal - $this->discount_amount;
        $this->tax_amount = ($afterDiscount * $this->tax_rate) / 100;
        
        // Calculate total
        $this->total = $afterDiscount + $this->tax_amount;
        
        // Calculate change
        $this->change_amount = $this->paid_amount - $this->total;
        
        $this->save();
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Check if transaction can be refunded
     */
    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' 
            && $this->created_at->diffInDays(now()) <= 7; // 7 hari
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * ✅ PERBAIKAN: Create cash flow otomatis untuk transaction
     */
    public function createCashFlow()
    {
        // Cek apakah sudah ada cash flow
        if ($this->cashFlow) {
            return $this->cashFlow;
        }

        return CashFlow::create([
            'type' => 'income',
            'source' => 'sale',
            'cash_flow_category_id' => null, // ✅ PERBAIKAN: Tambahkan field ini
            'amount' => $this->total,
            'description' => 'Penjualan - ' . $this->invoice_number,
            'reference_type' => self::class,
            'reference_id' => $this->id,
            'transaction_date' => $this->created_at->format('Y-m-d'),
            'payment_method' => $this->payment_method,
            'receipt_number' => $this->invoice_number, // ✅ PERBAIKAN: Gunakan invoice number
            'vendor_id' => null, // ✅ PERBAIKAN: Tambahkan field ini
            'created_by' => $this->user_id,
            'approval_status' => 'approved',
            'approved_by' => $this->user_id, // ✅ PERBAIKAN: Tambahkan field ini
            'approved_at' => now(), // ✅ PERBAIKAN: Tambahkan field ini
        ]);
    }

    /**
     * ✅ PERBAIKAN: Create refund cash flow
     */
    public function createRefundCashFlow()
    {
        return CashFlow::create([
            'type' => 'expense',
            'source' => 'refund',
            'cash_flow_category_id' => null, // ✅ PERBAIKAN: Tambahkan field ini
            'amount' => $this->total,
            'description' => 'Refund - ' . $this->invoice_number . ($this->refund_reason ? ' - ' . $this->refund_reason : ''),
            'reference_type' => self::class,
            'reference_id' => $this->id,
            'transaction_date' => now()->format('Y-m-d'),
            'payment_method' => $this->payment_method, // ✅ PERBAIKAN: Gunakan payment method yang sama
            'receipt_number' => $this->invoice_number, // ✅ PERBAIKAN: Tambahkan field ini
            'vendor_id' => null, // ✅ PERBAIKAN: Tambahkan field ini
            'created_by' => $this->refunded_by ?? auth()->id(),
            'approval_status' => 'approved',
            'approved_by' => $this->refunded_by ?? auth()->id(), // ✅ PERBAIKAN: Tambahkan field ini
            'approved_at' => now(), // ✅ PERBAIKAN: Tambahkan field ini
        ]);
    }
}