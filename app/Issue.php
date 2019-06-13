<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Issue extends Model
{
    protected $fillable = [
        'name', 'description', 'client_id', 'team_id', 'due_date'
    ];

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->name,
            '/issue/' . $this->id
        );
    }
}
