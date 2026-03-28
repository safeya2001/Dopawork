<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PortfolioItem extends Model
{
    protected $fillable = [
        'freelancer_profile_id', 'title', 'type', 'file_path', 'url', 'sort_order',
    ];

    public function profile()
    {
        return $this->belongsTo(FreelancerProfile::class, 'freelancer_profile_id');
    }

    public function getDisplayUrlAttribute(): string
    {
        if ($this->file_path) {
            return Storage::disk('public')->url($this->file_path);
        }
        return $this->url ?? '#';
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'image' => '🖼️',
            'pdf'   => '📄',
            default => '🔗',
        };
    }
}
