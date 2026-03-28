<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'subcategory_id', 'title', 'title_ar', 'slug',
        'description', 'description_ar', 'tags', 'cover_image', 'gallery',
        'status', 'rejection_reason', 'delivery_days', 'revisions',
        'is_featured', 'views', 'orders_count', 'rating', 'reviews_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function packages()
    {
        return $this->hasMany(ServicePackage::class)->orderBy('id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getDisplayTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getMinPriceAttribute(): float
    {
        return $this->packages->min('price') ?? 0;
    }
}
