<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueNote extends Model
{
    use HasFactory;

    protected $table = 'issues_notes';
    protected $guarded = [];

    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }
}
