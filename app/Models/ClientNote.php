<?php

namespace App\Models;

use App\Traits\HasActiveToggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'clients_notes';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
