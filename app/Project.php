<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name', 'description', 'user_id', 'client_id', 'team_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'users_projects');
        // return $this->belongsToMany('App\User', 'users_projects')->withTimestamps();
    }
}
