<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'client_id', 'freelancer_id', 'service_id', 'service_package_id',
        'title', 'requirements', 'status', 'subtotal', 'platform_fee', 'total_amount',
        'freelancer_earnings', 'delivery_days', 'deadline', 'revisions_allowed',
        'revisions_used', 'cancellation_reason', 'cancelled_by', 'delivered_at', 'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:3',
        'platform_fee' => 'decimal:3',
        'total_amount' => 'decimal:3',
        'freelancer_earnings' => 'decimal:3',
        'deadline' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class)->orderBy('sort_order');
    }

    public function escrow()
    {
        return $this->hasOne(EscrowTransaction::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function scopeForClient($query, int $userId)
    {
        return $query->where('client_id', $userId);
    }

    public function scopeForFreelancer($query, int $userId)
    {
        return $query->where('freelancer_id', $userId);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'in_progress', 'delivered', 'revision']);
    }

    public static function generateOrderNumber(): string
    {
        $count = static::withTrashed()->count() + 1;
        return 'DW-' . date('Y') . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
}
