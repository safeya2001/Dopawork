<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'user_id', 'type', 'amount', 'balance_before', 'balance_after',
        'description', 'description_ar', 'transactionable_id', 'transactionable_type',
        'proof_path', 'admin_note', 'meta', 'notes', 'reviewed_by', 'reviewed_at', 'status',
    ];

    protected $casts = [
        'amount'         => 'decimal:3',
        'balance_before' => 'decimal:3',
        'balance_after'  => 'decimal:3',
        'meta'           => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function getDisplayDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->description_ar
            ? $this->description_ar
            : ($this->description ?? '');
    }

    public static function generateReference(): string
    {
        return 'DW-TXN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
}
