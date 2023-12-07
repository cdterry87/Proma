<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUpload extends Model
{
    use HasFactory;

    protected $table = 'clients_uploads';
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
