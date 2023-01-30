<?php

namespace App\Models;

use App\Models\JobTitle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = ['competency', 'type'];

    public function peoples()
    {
        return $this->belongsToMany(People::class);
    }

    public function jobTitle()
    {
        return $this->belongsToMany(JobTitle::class)->withPivot('position');
    }

    public  function  scopePosition($q, string $position, $jobTitleId)
    {
        return $q->whereHas('jobTitle', function($query) use ($position, $jobTitleId) {
            $query->where('position', $position);
            // $query->whereIn('competency_id', $jobTitleId);
            $query->where('job_title_id', $jobTitleId);
        });
    }
}
