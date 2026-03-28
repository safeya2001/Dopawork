<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'category_id', 'title', 'description',
        'budget_type', 'budget_min', 'budget_max', 'deadline',
        'required_skills', 'attachments', 'preferred_location',
        'status', 'proposals_count',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'attachments'     => 'array',
        'deadline'        => 'date',
        'budget_min'      => 'decimal:3',
        'budget_max'      => 'decimal:3',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function proposals()
    {
        return $this->hasMany(ProjectProposal::class);
    }

    public function acceptedProposal()
    {
        return $this->hasOne(ProjectProposal::class)->where('status', 'accepted');
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order');
    }

    public function getBudgetRangeAttribute(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return number_format($this->budget_min, 3) . ' – ' . number_format($this->budget_max, 3) . ' JOD';
        }
        if ($this->budget_max) {
            return 'حتى ' . number_format($this->budget_max, 3) . ' JOD';
        }
        return 'مفتوح';
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
