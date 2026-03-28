<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'proposal_id', 'title', 'description',
        'amount', 'due_date', 'status',
        'delivery_note', 'revision_note',
        'delivered_at', 'approved_at', 'sort_order',
    ];

    protected $casts = [
        'amount'       => 'decimal:3',
        'due_date'     => 'date',
        'delivered_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function proposal()
    {
        return $this->belongsTo(ProjectProposal::class);
    }
}
