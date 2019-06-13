<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class ClientContact extends Model implements Searchable
{
    protected $table = 'clients_contacts';

    protected $fillable = [
        'client_id', 'name', 'title', 'email', 'phone'
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            'Contact: ' . $this->name,
            '/contact/' . $this->id
        );
    }
}
