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

    public function tasks()
    {
        return $this->hasMany('App\ProjectTask');
    }

    public function team()
    {
        return $this->belongsTo('App\Team');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
