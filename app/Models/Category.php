<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'parent_id',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot function untuk auto-generate UUID & Slug
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Auto-generate UUID
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            
            // Auto-generate Slug
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
                
                // Handle duplicate slugs
                $originalSlug = $model->slug;
                $count = 1;
                while (static::where('slug', $model->slug)->exists()) {
                    $model->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
        
        static::updating(function ($model) {
            // Auto-update slug if name changed and slug is empty
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
                
                // Handle duplicate slugs (excluding current model)
                $originalSlug = $model->slug;
                $count = 1;
                while (static::where('slug', $model->slug)
                    ->where('id', '!=', $model->id)
                    ->exists()) {
                    $model->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    /**
     * Relasi: Category punya banyak Products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi: Category bisa punya Parent Category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relasi: Category bisa punya banyak Child Categories
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Scope: Filter hanya category aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter hanya parent categories
     */
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get route key name untuk slug
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get full URL path untuk category
     */
    public function getUrlAttribute()
    {
        return route('boss.categories.show', $this->slug);
    }

    /**
     * Check if category has products
     */
    public function hasProducts()
    {
        return $this->products()->exists();
    }

    /**
     * Check if category has children
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Get total products count including children
     */
    public function getTotalProductsCountAttribute()
    {
        $count = $this->products()->count();
        
        foreach ($this->children as $child) {
            $count += $child->products()->count();
        }
        
        return $count;
    }
}