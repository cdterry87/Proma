<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueTask extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'issues_tasks';

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
