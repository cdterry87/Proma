<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamUpload extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'teams_uploads';

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
