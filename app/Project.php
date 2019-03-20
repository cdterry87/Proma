<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name', 'description', 'client_id', 'team_id',
    ];

    public function user()
    {
        return $this->belongsToMany('App\User', 'users_projects')->withTimestamps();
    }
}
