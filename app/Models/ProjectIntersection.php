<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectIntersection extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function parentProject()
    {
        return $this->belongsTo(ParentProject::class);
    }
}
