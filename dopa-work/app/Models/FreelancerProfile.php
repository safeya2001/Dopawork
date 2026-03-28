<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'professional_title', 'professional_title_ar', 'overview', 'overview_ar',
        'skills', 'languages', 'education', 'portfolio_url', 'portfolio_links',
        'certifications', 'category_ids',
        'hourly_rate', 'experience_level', 'total_orders', 'completed_orders',
        'rating', 'total_reviews', 'is_verified', 'is_featured', 'is_available', 'member_since',
    ];

    protected $casts = [
        'skills'         => 'array',
        'languages'      => 'array',
        'portfolio_links'=> 'array',
        'certifications' => 'array',
        'category_ids'   => 'array',
        'hourly_rate'    => 'decimal:3',
        'rating'         => 'decimal:2',
        'is_verified'    => 'boolean',
        'is_featured'    => 'boolean',
        'is_available'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portfolioItems()
    {
        return $this->hasMany(PortfolioItem::class)->orderBy('sort_order');
    }

    public function categories()
    {
        return $this->category_ids
            ? Category::whereIn('id', $this->category_ids)->get()
            : collect();
    }

    public function getDisplayTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->professional_title_ar
            ? $this->professional_title_ar
            : ($this->professional_title ?? '');
    }
}
