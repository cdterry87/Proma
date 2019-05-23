<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function user()
    {
        return $this->belongsToMany('App\User', 'users_clients')->withTimestamps();
    }

    public function contacts()
    {
        return $this->hasMany('App\ClientContact');
    }

    public function project()
    {
        return $this->hasOne('App\Project');
    }
}
