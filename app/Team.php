<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'users_teams')->withTimestamps();
    }

    public function members()
    {

    }
}
