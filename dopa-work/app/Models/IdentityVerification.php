<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'document_type', 'document_number', 'front_image', 'back_image',
        'selfie_image', 'status', 'rejection_reason', 'rejection_reason_ar',
        'reviewed_by', 'reviewed_at', 'document_expiry',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'document_expiry' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
