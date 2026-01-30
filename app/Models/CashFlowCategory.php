<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CashFlowCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'type',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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
        });

        static::updating(function ($model) {
            if ($model->isDirty('name')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Relasi: Category punya banyak Cash Flows
     */
    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class, 'cash_flow_category_id');
    }

    /**
     * Scope: Active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * ✅ TAMBAHAN: Scope by type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Income categories
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope: Expense categories
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Get type badge color
     */
    public function getTypeBadgeColor(): string
    {
        return $this->type === 'income' ? 'success' : 'danger';
    }

    /**
     * Get type label
     */
    public function getTypeLabel(): string
    {
        return $this->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
    }

    /**
     * Get formatted color
     */
    public function getFormattedColorAttribute(): string
    {
        return $this->color ?? ($this->type === 'income' ? '#10B981' : '#EF4444');
    }

    /**
     * Get icon with default
     */
    public function getIconWithDefault(): string
    {
        if ($this->icon) {
            return $this->icon;
        }
        
        return $this->type === 'income' ? 'fa-arrow-up' : 'fa-arrow-down';
    }
}