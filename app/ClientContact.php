<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    protected $table = 'clients_contacts';

    protected $fillable = [
        'client_id', 'name', 'title', 'email', 'phone'
    ];
}
