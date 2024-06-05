<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueUpload extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'issues_uploads';

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
