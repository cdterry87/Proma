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
        return $this->belongsTo('App\User')->withTimestamps();
    }

    public function contacts()
    {
        return $this->hasMany('App\ClientContact');
    }

    public function project()
    {
        return $this->hasMany('App\Project');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->name,
            '/client/' . $this->id
        );
    }
}
