<?php

namespace App\Models;

use App\Models\Event;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class People extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'peoples';

    protected $fillable = ['name', 'nik', 'org', 'position', 'job_title_id','email'];

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
        return $this->belongsTo(JobTitle::class)->withTrashed()->select(['name', 'id']);
    }

    public function showSkills()
    {
        $skills = $this->competencies->groupBy('type')->all();

        $defaultSkill = [
            'hard' => isset($skills['hard']) ? $skills['hard'] : [],
            'soft' => isset($skills['soft']) ? $skills['soft'] : [], 
            'doa' => isset($skills['doa']) ? $skills['doa'] : []
        ];

        return $defaultSkill;
    }
}
