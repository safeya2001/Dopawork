<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'reviewer_id', 'reviewee_id', 'service_id',
        'rating', 'communication_rating', 'quality_rating', 'delivery_rating',
        'comment', 'comment_ar', 'is_public',
    ];

    protected $casts = ['is_public' => 'boolean'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getDisplayCommentAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->comment_ar ? $this->comment_ar : ($this->comment ?? '');
    }
}
