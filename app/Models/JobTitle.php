<?php

namespace App\Models;

use App\Models\Event;
use App\Models\People;
use App\Models\Competency;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
class JobTitle extends Model implements Searchable
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name',];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function peoples(){
        return $this->hasMany(People::class);
    }
    
    public function competencies(){
        return $this->belongsToMany(Competency::class)->withPivot('position');
    }

    public function getSearchResult(): SearchResult
    {
        return new \Spatie\Searchable\SearchResult(
           $this,
           $this->name,
           $this->id
        );
    }
    
    public function showSkills($position=null)
    {
        $skills = $this->competencies->where('pivot.position', $position)->groupBy('type')->all();

        collect($skills)->map(function ($skill) {
            return $skill->map(function ($competency) {
                $event = Event::where('competency_id', $competency->id)->first();

                if ($event) {
                    $startDate = Carbon::parse($event->fullStartDate)->format('Y-m-d H:i');
                    $competency['start_date'] = $startDate;
                }

                return $competency;
            });
        });

        $defaultSkill = ['hard' => isset($skills['hard']) ? $skills['hard'] : [] , 'soft' => isset($skills['soft']) ? $skills['soft'] : [] , 'doa' => isset($skills['doa']) ? $skills['doa'] : [] ];

        return $defaultSkill;
    }


}
