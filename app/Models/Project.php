<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    public function projectData()
    {
        return $this->hasMany(ProjectTimeData::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get the first inserted child model
    public function FirstProjectData()
    {
        return $this->hasOne(ProjectTimeData::class)->oldestOfMany();
    }
    public function lastProjectData()
    {
        return $this->hasOne(ProjectTimeData::class)->latestOfMany();
    }

    public function parentProject()
    {
        return $this->belongsTo(ParentProject::class);
    }

}
