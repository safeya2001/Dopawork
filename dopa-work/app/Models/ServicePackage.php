<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'type', 'name', 'name_ar', 'description', 'description_ar',
        'price', 'delivery_days', 'revisions', 'features', 'features_ar', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'features' => 'array',
        'features_ar' => 'array',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }
}
