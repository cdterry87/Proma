<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'message'
    ];

    protected $appends = ['elapsed_time'];

    public function getElapsedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createNotification($message)
    {
        return $this->create([
            'user_id' => auth()->id(),
            'message' => $message
        ]);
    }
}
