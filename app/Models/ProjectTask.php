<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'projects_tasks';

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
