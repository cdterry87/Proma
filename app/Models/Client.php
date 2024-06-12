<?php

namespace App\Models;

use App\Traits\HasActiveToggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasActiveToggle;
    use HasFactory;

    protected $guarded = [];
    protected $table = 'clients';

    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }

    public function uploads()
    {
        return $this->hasMany(ClientUpload::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function getIncompleteProjects()
    {
        return $this->projects()->whereNull('completed_date');
    }

    public function getCompleteProjects()
    {
        return $this->projects()->whereNotNull('completed_date');
    }

    public function getIncompleteIssues()
    {
        return $this->issues()->whereNull('resolved_date');
    }

    public function getCompleteIssues()
    {
        return $this->issues()->whereNotNull('resolved_date');
    }
}
