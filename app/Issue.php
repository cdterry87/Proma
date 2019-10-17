<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Issue extends Model implements Searchable
{
    protected $fillable = [
        'description', 'priority', 'user_id', 'project_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User')->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany('App\IssueNote');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->description,
            '/issue/' . $this->id
        );
    }

    public function uploads()
    {
        return $this->morphMany('App\Upload', 'uploadable');
    }
}
