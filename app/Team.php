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

    public function members()
    {
        return $this->hasMany('App\TeamMember')->with('user');
    }
}
