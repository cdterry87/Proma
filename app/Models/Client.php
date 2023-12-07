<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';
    protected $guarded = [];

    public function notes()
    {
        return $this->hasMany(ClientNote::class);
    }

    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function uploads()
    {
        return $this->hasMany(ClientUpload::class);
    }
}
