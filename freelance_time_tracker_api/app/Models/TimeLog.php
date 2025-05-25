<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
class TimeLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'start_time',
        'end_time',
        'description',
        'hours',
        'tag'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    use HasFactory;
}