<?php

namespace App\Models;

use App\Traits\HasActiveToggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasActiveToggle;
    use HasFactory;

    protected $guarded = [];

    protected $table = 'projects';
}
