<?php

namespace App\Models;

use App\Models\People;
use App\Models\Competency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTitle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function peoples(){
        return $this->hasMany(People::class);
    }
    
    public function competencies(){
        return $this->belongsToMany(Competency::class)->withPivot('position');
    }

    
    public function showSkills($jobTitle, $position)
    {
        $skill = $jobTitle->competencies->where('pivot.position', $position)->groupBy('type')->all();

        $defaultSkill = ['hard' => isset($skill['hard']) ? $skill['hard'] : [] , 'soft' => isset($skill['soft']) ? $skill['soft'] : [] , 'doa' => isset($skill['doa']) ? $skill['doa'] : [] ];

        return $defaultSkill;
    }


}
