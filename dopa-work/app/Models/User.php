<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'name_ar', 'company_name', 'email', 'phone', 'password', 'role', 'status',
        'avatar', 'locale', 'country', 'city', 'bio', 'bio_ar', 'wallet_balance',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'wallet_balance' => 'decimal:3',
    ];

    // --- Relationships ---

    public function freelancerProfile()
    {
        return $this->hasOne(FreelancerProfile::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function ordersAsClient()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function ordersAsFreelancer()
    {
        return $this->hasMany(Order::class, 'freelancer_id');
    }

    public function identityVerification()
    {
        return $this->hasOne(IdentityVerification::class)->latest();
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function notifications()
    {
        return $this->hasMany(PlatformNotification::class);
    }

    public function conversationsAsClient()
    {
        return $this->hasMany(Conversation::class, 'client_id');
    }

    public function conversationsAsFreelancer()
    {
        return $this->hasMany(Conversation::class, 'freelancer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    // --- Helpers ---

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isFreelancer(): bool
    {
        return $this->role === 'freelancer';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isVerified(): bool
    {
        return $this->identityVerification && $this->identityVerification->status === 'approved';
    }

    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }
}
