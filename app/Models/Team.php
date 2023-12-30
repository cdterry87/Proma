<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';
    protected $guarded = [];

    public function uploads()
    {
        return $this->hasMany(TeamUpload::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function members()
    {
        return $this->hasMany(TeamUser::class);
    }
}
