<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'projects_tasks';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function dueNotificationSent()
    {
        return $this->hasOne(ProjectTaskNotificationSent::class)
            ->where('due', true)
            ->where('completed', false);
    }

    public function overdueNotificationSent()
    {
        return $this->hasOne(ProjectTaskNotificationSent::class)
            ->where('overdue', true)
            ->where('completed', false);
    }

    public function completeNotificationSent()
    {
        return $this->hasOne(ProjectTaskNotificationSent::class)
            ->where('completed', true);
    }
}
