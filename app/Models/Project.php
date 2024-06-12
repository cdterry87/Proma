<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'projects';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function getIncompleteIssues()
    {
        return $this->issues()->whereNull('completed_date');
    }

    public function getCompleteIssues()
    {
        return $this->issues()->whereNotNull('completed_date');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
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
        return $this->hasMany(ProjectUpload::class);
    }
}
