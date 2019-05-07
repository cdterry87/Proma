<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'users_clients')->withTimestamps();
    }

    public function contacts()
    {

    }
}
