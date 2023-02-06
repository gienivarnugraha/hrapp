<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Models\People;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Support\Arr;
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
        $paginator = People::with('jobTitle')->orderBy('job_title_id')->simplePaginate($perpage)->toArray();
        $paginator['total']= People::count();
        $paginator['total_page']= ceil( People::count() / $perpage) ;


        return response()->json($paginator);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $people =  People::create($request->only(['name','nik','org','position','job_title_id']));

        $people->job_title_id = $request->job_title_id;

        $skills = Arr::flatten($request->input('skills'), 1);

        $people->save();

        $people->competencies()->attach($skills);

        return response()->json($people);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JobTitle  $jobTitle
     * @return \Illuminate\Http\Response
     */
    public function show(People $people)
    {
        $jobs = JobTitle::find($people->jobTitle->id);

        $requiredSkills = [
            'junior' => $jobs->showSkills('junior'),
            'medior' => $jobs->showSkills('medior'),
            'senior' => $jobs->showSkills('senior'),
        ];
        
        return response()->json([
            'skills' =>  $people->showSkills(),
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
        $people->update($request->only(['name','nik','org','position']));

        if ($request->skills) {
            $forSync = collect($request->skills)->map(function ($value, $index) use ($people, $request) {

                $sync= collect($request->skills[$index])->reduce(function ($prev, $next) {
                    $prev[] = $next['id'];
                    return $prev;
                }, []);

                return $sync;

            });

            $flatten = Arr::flatten($forSync,1);

            $people->competencies()->sync($flatten);
        }

        $jobs = JobTitle::find($people->jobTitle->id);

        $requiredSkills = [
            'junior' => $jobs->showSkills('junior'),
            'medior' => $jobs->showSkills('medior'),
            'senior' => $jobs->showSkills('senior'),
        ];

        $people['skills'] = $people->showSkills();
        
        $people['job_title'] = $people->jobTitle;

        $people['required_skills'] = $requiredSkills;

        return response()->json($people);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function destroy(People $people)
    {
        $people->delete();

        return response()->json(['status' => 'success']);
    }
}
