<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Models\People;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\PeopleResource;
use App\Http\Resources\PeopleCollection;
use Illuminate\Database\Eloquent\Builder;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perpage = 25;
        $paginator = People::with('jobTitle')->orderBy('id')->simplePaginate($perpage)->toArray();
        $paginator['total']= People::count();
        $paginator['total_page']= ceil( People::count() / $perpage) ;


        return response()->json($paginator);
    }

    public function getSkills($competencies)
    {
        $skills = collect([
            ['id' => 'hard', 'type' => 'subheader', 'name' => 'Hard Skills'],
            ['id' => 'divider#1', 'type' => 'divider'],
            ['id' => 'soft', 'type' => 'subheader', 'name' => 'Soft Skills'],
            ['id' => 'divider#2', 'type' => 'divider'],
            ['id' => 'doa', 'type' => 'subheader', 'name' => 'DOA'],
        ]);

        $competencies->each(function ($competency) use ($skills) {
            $index = -1;

            if ($competency->type === 'hard') {
                $index = $skills->search(fn ($skl) => $skl["id"] === 'hard');
            } else if ($competency->type === 'soft') {
                $index = $skills->search(fn ($skl) => $skl["id"] === 'soft');
            } else if ($competency->type === 'doa') {
                $index = $skills->search(fn ($skl) => $skl["id"] === 'doa');
            }

            $event = Event::where('competency_id', $competency->id)->first();

            if ($event == null) {
                $skill = ['type' => 'item', 'name' => "{$competency->name}", 'value' => $competency->id, 'id' => 'competency-' . $competency->id];
            } else {
                $startDate = Carbon::parse($event->fullStartDate)->format('Y-m-d H:i');
                $skill = ['type' => 'item', 'name' => $competency->name, 'start_date' => $startDate, 'value' => $competency->id, 'id' => 'competency-' . $competency->id];
            }


            $skills->splice($index + 1, 0, [$skill]);
        });

        return $skills;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function show(People $people)
    {
        $people = People::find($people->id);

        $competencies = JobTitle::find($people->jobTitle->id)->competencies;

        $junior = $competencies->where('pivot.position', 'junior');
        $medior = $competencies->where('pivot.position', 'medior');
        $senior = $competencies->where('pivot.position', 'senior');

        $requiredSkills = [
            'junior' => $this->getSkills($junior),
            'medior' => $this->getSkills($medior),
            'senior' => $this->getSkills($senior),
        ];
        
        return response()->json([
            'skills' => $this->getSkills($people->competencies),
            'required_skills' => $requiredSkills,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, People $people)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function destroy(People $people)
    {
        //
    }
}
