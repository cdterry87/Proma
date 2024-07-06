<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'projects_tasks';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
