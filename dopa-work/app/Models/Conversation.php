<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'freelancer_id', 'order_id', 'service_id', 'last_message_at'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function getOtherParticipant(int $userId): ?User
    {
        if ($this->client_id === $userId) {
            return $this->freelancer;
        }
        return $this->client;
    }
}
