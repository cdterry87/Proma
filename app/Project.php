<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Project extends Model implements Searchable
{
    protected $fillable = [
        'user_id', 'name', 'description', 'client_id', 'due_date'
    ];

    public function user()
    {
        return $this->belongsTo('App\User')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany('App\ProjectTask');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function issues()
    {
        return $this->hasMany('App\Issue');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->name,
            '/project/' . $this->id
        );
    }

    public function uploads()
    {
        return $this->morphMany('App\Upload', 'uploadable');
    }
}
