<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_post_id', 'freelancer_id', 'cover_letter', 'proposed_amount',
        'delivery_days', 'status', 'milestones_plan',
    ];

    protected $casts = [
        'proposed_amount' => 'decimal:3',
        'milestones_plan' => 'array',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
