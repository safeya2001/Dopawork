<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'title', 'title_ar', 'description', 'amount', 'sort_order',
        'status', 'due_date', 'delivered_at', 'approved_at', 'delivery_note', 'attachments',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'attachments' => 'array',
        'due_date' => 'datetime',
        'delivered_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
