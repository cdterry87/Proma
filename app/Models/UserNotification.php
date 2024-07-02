<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'users_notifications';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
