<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformNotification extends Model
{
    use HasFactory;

    protected $table = 'platform_notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'title_ar', 'body', 'body_ar',
        'data', 'action_url', 'is_read', 'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    public function getDisplayBodyAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->body_ar ? $this->body_ar : $this->body;
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }
}
