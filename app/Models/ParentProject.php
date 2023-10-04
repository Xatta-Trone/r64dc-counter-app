<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentProject extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function intersections()
    {
        return $this->hasMany(ProjectIntersection::class);
    }
}
