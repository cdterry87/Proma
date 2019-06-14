<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class ProjectTask extends Model implements Searchable
{
    protected $table = 'projects_tasks';

    protected $fillable = [
        'project_id', 'description', 'due_date'
    ];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->description,
            '/project/' . $this->project_id
        );
    }
}
