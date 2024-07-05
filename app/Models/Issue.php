<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Issue extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'issues';

    protected $casts = [
        'resolved_date' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(IssueTask::class);
    }

    public function incomplete_tasks()
    {
        return $this->tasks()->whereNull('completed_date');
    }

    public function complete_tasks()
    {
        return $this->tasks()->whereNotNull('completed_date');
    }

    public function uploads()
    {
        return $this->hasMany(IssueUpload::class);
    }

    public static function getPriorityCodes()
    {
        return collect(
            [
                ['value' => 1,  'label' => 'Low', 'class' => 'bg-success text-black'],
                ['value' => 2,  'label' => 'Medium', 'class' => 'bg-info text-black'],
                ['value' => 3,  'label' => 'High', 'class' => 'bg-warning text-black'],
                ['value' => 4,  'label' => 'Critical', 'class' => 'bg-error text-black'],
            ]
        );
    }

    public static function getResolvedCodes()
    {
        return [
            ['value' => 0,  'label' => 'Open'],
            ['value' => 1,  'label' => 'Closed']
        ];
    }
}
