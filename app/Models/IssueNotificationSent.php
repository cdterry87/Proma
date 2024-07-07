<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueNotificationSent extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'issues_notifications_sent';
}
