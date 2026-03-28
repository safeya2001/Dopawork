<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'order_id', 'raised_by', 'against', 'subject', 'description',
        'attachments', 'status', 'resolution', 'resolution_notes',
        'client_refund_amount', 'freelancer_release_amount', 'resolved_by', 'resolved_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'client_refund_amount' => 'decimal:3',
        'freelancer_release_amount' => 'decimal:3',
        'resolved_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function raisedBy()
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    public function against()
    {
        return $this->belongsTo(User::class, 'against');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public static function generateReference(): string
    {
        return 'DW-DIS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
