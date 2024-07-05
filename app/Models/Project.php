<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'projects';

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function unresolved_issues()
    {
        return $this->issues()->whereNull('resolved_date');
    }

    public function resolved_issues()
    {
        return $this->issues()->whereNotNull('resolved_date');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function incomplete_tasks()
    {
        return $this->tasks()->whereNull('completed_date');
    }

    public function complete_tasks()
    {
        return $this->tasks()->whereNotNull('completed_date');
    }

    public function uploads()
    {
        return $this->hasMany(ProjectUpload::class);
    }
}
