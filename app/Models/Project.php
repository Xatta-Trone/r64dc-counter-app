<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

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

}
