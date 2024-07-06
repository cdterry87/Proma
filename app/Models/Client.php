<?php

namespace App\Models;

use App\Traits\WithActiveToggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use WithActiveToggle;
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

    public function incomplete_projects()
    {
        return $this->projects()->whereNull('completed_date');
    }

    public function complete_projects()
    {
        return $this->projects()->whereNotNull('completed_date');
    }

    public function unresolved_issues()
    {
        return $this->issues()->whereNull('resolved_date');
    }

    public function resolved_issues()
    {
        return $this->issues()->whereNotNull('resolved_date');
    }
}
