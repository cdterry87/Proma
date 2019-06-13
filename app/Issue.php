<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Issue extends Model
{
    protected $fillable = [
        'description', 'priority',
    ];

    public function user()
    {
        return $this->belongsTo('App\User')->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany('App\IssueActivity');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->name,
            '/issue/' . $this->id
        );
    }
}
