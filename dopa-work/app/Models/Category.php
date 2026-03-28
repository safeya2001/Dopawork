<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_ar', 'slug', 'icon', 'description', 'description_ar',
        'parent_id', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }
}
