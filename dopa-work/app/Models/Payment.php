<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'user_id', 'order_id', 'escrow_id', 'amount', 'currency',
        'payment_method', 'type', 'status', 'gateway_reference', 'gateway_response',
        'cliq_alias', 'bank_account', 'notes', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function escrow()
    {
        return $this->belongsTo(EscrowTransaction::class, 'escrow_id');
    }

    public static function generateReference(): string
    {
        return 'DW-PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
}
