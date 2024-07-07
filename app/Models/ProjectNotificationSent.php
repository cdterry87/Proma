<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectNotificationSent extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'projects_notifications_sent';
}
