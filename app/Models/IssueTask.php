<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueTask extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'issues_tasks';

    protected $casts = [
        'completed_date' => 'datetime',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
