<?php

namespace App\Http\Controllers\API;

use App\Models\JobTitle;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

use function PHPUnit\Framework\isEmpty;

class JobTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = JobTitle::simplePaginate(10);

        return response()->json($jobs);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function show(JobTitle $jobTitle)
    {
        $skills = [
            'junior' => $jobTitle->showSkills('junior'),
            'medior' => $jobTitle->showSkills('medior'),
            'senior' => $jobTitle->showSkills('senior'),
        ];
        // jobTitle->competencies->groupBy(['pivot.position', 'type'])->all()

        return response()->json([
            'competencies' => $skills,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $job = JobTitle::create($request->only('name'));

        return response()->json($job);
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
        $jobTitle->update($request->only(['name', 'competencies', 'position']));

        if ($request->competencies) {
            $jobTitle->competencies()->detach();

            collect($request->competencies)->map(function ($value, $index) use ($jobTitle, $request) {
                $competenciesId = collect($request->competencies[$index])->reduce(function ($prev, $next) {
                    $nx = collect($next)->pluck('id')->all();
                    $prev[] = $nx;
                    return $prev;
                }, []);

                $flatten = Arr::flatten($competenciesId);

                $sync = array_fill_keys($flatten, ['position' => $index]);

                $jobTitle->competencies()->attach($sync);
            });

        }

        $jobTitle['skills'] = [
            'junior' => $jobTitle->showSkills($jobTitle, 'junior'),
            'medior' => $jobTitle->showSkills($jobTitle, 'medior'),
            'senior' => $jobTitle->showSkills($jobTitle, 'senior'),
        ];

        return response()->json($jobTitle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobTitle $jobTitle)
    {
        $jobTitle->delete();

        return response()->json(['status' => 'success']);
    }

    
    public function getAll(){
        $jobs = JobTitle::select(['id','name'])->get();

        return response()->json($jobs);
    }

}
