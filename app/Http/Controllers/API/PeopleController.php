<?php

namespace App\Http\Controllers\API;

use App\Models\People;
use App\Models\Competency;
use Illuminate\Http\Request;
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
        $peoples = People::with(['jobTitle', 'competencies'])->get();

        $peoples->map(function ($people) {
            // $requiredSkillsList = $people->jobTitle->competencies->pluck('id');

            // $currentSkillsList = $people->competencies->pluck('id');

            // $diffId = $requiredSkillsList->diff($currentSkillsList)->all();

            $jobTitleId = $people->jobTitle->id;


            $requiredSkills = [
                'junior' => $this->getSkills(Competency::position('junior', $jobTitleId)->get()),
                'senior' => $this->getSkills(Competency::position('senior', $jobTitleId)->get()),
                'medior' => $this->getSkills(Competency::position('medior', $jobTitleId)->get()),
            ];

            $people['skills'] = $this->getSkills($people->competencies);

            $people['required_skills'] = $requiredSkills;

            return $people;
        });

        return response($peoples);
    }

    public function getSkills($competencies){
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

            $skill = ['name' => $competency->name, 'value' => $competency->id, 'id' => 'competency-' . $competency->id];

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
        //
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
