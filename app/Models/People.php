<?php

namespace App\Models;

use App\Models\Event;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class People extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'peoples';

    public function competencies()
    {
        return $this->belongsToMany(Competency::class)->orderBy('type');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }
}
