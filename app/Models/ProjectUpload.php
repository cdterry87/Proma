<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUpload extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'projects_uploads';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
