<?php

namespace App\Models;

use App\Traits\HasActiveToggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasActiveToggle;
    use HasFactory;

    protected $guarded = [];
    protected $table = 'clients';

    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }

    public function notes()
    {
        return $this->hasMany(ClientNote::class);
    }

    public function uploads()
    {
        return $this->hasMany(ClientUpload::class);
    }
}
