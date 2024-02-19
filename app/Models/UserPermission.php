<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $table = 'users_permissions';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
