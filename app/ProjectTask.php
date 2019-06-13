<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    protected $table = 'projects_tasks';

    protected $fillable = [
        'project_id', 'description', 'due_date'
    ];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
