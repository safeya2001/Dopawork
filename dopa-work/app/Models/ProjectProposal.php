<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'freelancer_id', 'cover_letter',
        'budget', 'delivery_days', 'status',
    ];

    protected $casts = [
        'budget' => 'decimal:3',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class, 'proposal_id')->orderBy('sort_order');
    }
}
