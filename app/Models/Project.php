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

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function uploads()
    {
        return $this->hasMany(ProjectUpload::class);
    }
}
