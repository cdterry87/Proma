<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'teams';

    public function users()
    {
        return $this->belongsToMany(User::class, 'teams_users', 'team_id', 'user_id');
    }
}
