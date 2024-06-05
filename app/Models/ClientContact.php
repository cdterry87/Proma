<?php

namespace App\Models;

use App\Traits\HasActiveToggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientContact extends Model
{
    use HasActiveToggle;
    use HasFactory;

    protected $guarded = [];
    protected $table = 'clients_contacts';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
