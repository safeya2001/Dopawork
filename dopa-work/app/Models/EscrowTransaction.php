<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscrowTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'order_id', 'client_id', 'freelancer_id', 'milestone_id',
        'amount', 'platform_fee', 'freelancer_amount', 'status', 'payment_method',
        'payment_reference', 'held_at', 'released_at', 'refunded_at',
        'auto_release_at', 'released_by', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'platform_fee' => 'decimal:3',
        'freelancer_amount' => 'decimal:3',
        'held_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'auto_release_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function isHeld(): bool
    {
        return $this->status === 'held';
    }

    public static function generateReference(): string
    {
        return 'DW-ESC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
