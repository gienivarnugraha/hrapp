<?php

namespace App\Http\Controllers\API;

use App\Models\JobTitle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class JobTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = JobTitle::paginate(25);

        $jobs->map(function($job){
            return $job['skills'] = $this->getSkills($job->competencies);
        });

        
        return response()->json($jobs);
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
    public function show(JobTitle $jobTitle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobTitle $jobTitle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobTitle $jobTitle)
    {
        //
    }
}
