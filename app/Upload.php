<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = ['name', 'filepath', 'uploadable_id', 'uploadable_type'];

    public function uploadable()
    {
        return $this->morphTo();
    }
}
