<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'category_id', 'title', 'title_ar', 'description', 'description_ar',
        'skills_required', 'budget_min', 'budget_max', 'budget_type', 'duration_days',
        'experience_level', 'status', 'expires_at', 'proposals_count',
    ];

    protected $casts = [
        'skills_required' => 'array',
        'budget_min' => 'decimal:3',
        'budget_max' => 'decimal:3',
        'expires_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function getDisplayTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }
}
