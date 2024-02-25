<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'clients_contacts';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
