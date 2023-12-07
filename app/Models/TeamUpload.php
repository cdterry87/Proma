<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamUpload extends Model
{
    use HasFactory;

    protected $table = 'teams_uploads';
    protected $guarded = [];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
