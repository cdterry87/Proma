<?php

namespace App\Models;

use App\Traits\HasActiveToggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasActiveToggle;
    use HasFactory;

    protected $guarded = [];

    protected $table = 'teams';

    public function users()
    {
        return $this->belongsToMany(User::class, 'teams_users', 'team_id', 'user_id');
    }

    public function uploads()
    {
        return $this->hasMany(TeamUpload::class);
    }
}
