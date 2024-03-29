<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueAssignment extends Model
{
    use HasFactory;

    protected $table = 'issues_assignments';
    protected $guarded = [];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
