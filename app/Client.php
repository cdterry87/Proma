<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Client extends Model implements Searchable
{
    protected $fillable = [
        'name', 'description'
    ];

    public function user()
    {
        return $this->belongsToMany('App\User', 'users_clients')->withTimestamps();
    }

    public function contacts()
    {
        return $this->hasMany('App\ClientContact');
    }

    public function project()
    {
        return $this->hasOne('App\Project');
    }

    public function getSearchResult() : SearchResult
    {
        return new SearchResult(
            $this,
            'Client: ' . $this->name,
            '/client/' . $this->id
        );
    }

}
