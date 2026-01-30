<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CashFlow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'type',
        'source',
        'cash_flow_category_id',
        'amount',
        'description',
        'reference_type',
        'reference_id',
        'transaction_date',
        'payment_method',
        'receipt_number',
        'receipt_file',
        'vendor_id',
        'created_by',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'approval_notes', // ✅ NEW: untuk menyimpan alasan review
    ];

    protected $casts = [
        'amount' => 'integer',
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            
            if (empty($model->transaction_date)) {
                $model->transaction_date = now()->format('Y-m-d');
            }
        });
    }

    // RELATIONS
    public function category()
    {
        return $this->belongsTo(CashFlowCategory::class, 'cash_flow_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'reference_id')
            ->where('reference_type', 'Transaction');
    }

    public function productVendorPrice()
    {
        return $this->belongsTo(ProductVendorPrice::class, 'reference_id')
            ->where('reference_type', 'ProductVendorPrice');
    }

    // SCOPES
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeSource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeFromSales($query)
    {
        return $query->where('source', 'sale');
    }

    public function scopeFromPurchases($query)
    {
        return $query->where('source', 'purchase');
    }

    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', now());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('transaction_date', now()->year)
                     ->whereMonth('transaction_date', now()->month);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('transaction_date', now()->year);
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('cash_flow_category_id', $categoryId);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    // HELPERS
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getTypeBadgeColor(): string
    {
        return $this->type === 'income' ? 'success' : 'danger';
    }

    public function getTypeLabel(): string
    {
        return $this->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
    }

    public function getSourceLabel(): string
    {
        $labels = [
            'sale' => 'Penjualan',
            'purchase' => 'Pembelian',
            'manual' => 'Manual',
            'refund' => 'Refund',
            'adjustment' => 'Penyesuaian',
            'other' => 'Lainnya',
        ];
        
        return $labels[$this->source] ?? $this->source;
    }

    public function getApprovalBadgeColor(): string
    {
        return [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ][$this->approval_status] ?? 'secondary';
    }

    public function getApprovalStatusLabel(): string
    {
        return [
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ][$this->approval_status] ?? $this->approval_status;
    }

    public function canBeApproved(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function canBeEdited(): bool
    {
        return $this->source === 'manual' && $this->approval_status !== 'approved';
    }

    public function canBeDeleted(): bool
    {
        return $this->source === 'manual';
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function getCategoryNameAttribute(): string
    {
        if ($this->category) {
            return $this->category->name;
        }
        
        return $this->getSourceLabel();
    }

    public function getPaymentMethodLabel(): string
    {
        $labels = [
            'cash' => 'Tunai',
            'bank_transfer' => 'Transfer Bank',
            'debit_card' => 'Kartu Debit',
            'credit_card' => 'Kartu Kredit',
            'qris' => 'QRIS',
            'other' => 'Lainnya',
        ];
        
        return $labels[$this->payment_method] ?? '-';
    }
}