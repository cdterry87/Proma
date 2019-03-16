<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name', 'description', 'user_id'
    ];

    public function user()
    {
        return $this->belongsToMany('App\User', 'users_teams')->withTimestamps();
    }

}
