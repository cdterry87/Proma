<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function user()
    {
        return $this->belongsToMany('App\User', 'users_teams')->withTimestamps();
    }

    public function member()
    {
        return $this->belongsToMany('App\TeamMember', 'teams_members', 'team_id', 'user_id')->withTimestamps();
    }

    public function members()
    {
        return $this->hasMany('App\TeamMember')->with('user');
    }
}
