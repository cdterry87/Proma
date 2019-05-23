<?php

namespace App;

use Illuminate\Support\Facades\DB;
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

    public function project()
    {
        return $this->hasOne('App\Project');
    }

    public static function availableUsers(Team $team)
    {
        return DB::table("users")->select('*')->whereNotIn('users.id', function ($query) use ($team) {
            $query->select('user_id')->from('teams_members')->where('team_id', $team->id);
        })->get();
    }
}
