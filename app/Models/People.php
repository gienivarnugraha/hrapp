<?php

namespace App\Models;

use DB;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class People extends Model
{
    use HasFactory;

    protected $table = 'peoples';

    public function competencies()
    {
        return $this->belongsToMany(Competency::class)->orderBy('type');
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }
}
