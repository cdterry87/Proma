<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Project extends Model implements Searchable
{
    protected $fillable = [
        'name', 'description', 'client_id', 'team_id',
    ];

    public function user()
    {
        return $this->belongsToMany('App\User', 'users_projects')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany('App\ProjectTask');
    }

    public function team()
    {
        return $this->belongsTo('App\Team');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function getSearchResult() : SearchResult
    {
        return new SearchResult(
            $this,
            $this->name,
            '/project/' . $this->id
        );
    }

}
