<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueActivity extends Model
{
    protected $table = 'issues_activities';

    protected $fillable = [
        'issue_id', 'description'
    ];

    public function issue()
    {
        return $this->belongsTo('App\Issue');
    }
}
