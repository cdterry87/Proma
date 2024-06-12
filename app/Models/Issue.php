<?php

namespace App\Models;

use App\Traits\HasActiveToggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Issue extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'issues';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(IssueTask::class);
    }

    public function getIncompleteTasks()
    {
        return $this->tasks()->whereNull('completed_date');
    }

    public function getCompleteTasks()
    {
        return $this->tasks()->whereNotNull('completed_date');
    }

    public function uploads()
    {
        return $this->hasMany(IssueUpload::class);
    }
}
