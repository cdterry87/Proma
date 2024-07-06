<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientUpload extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'clients_uploads';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
