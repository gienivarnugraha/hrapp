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
        $competencies = JobTitle::find($people->jobTitle->id)->competencies;

        $junior = $competencies->where('pivot.position', 'junior')->groupBy('type')->all();
        $medior = $competencies->where('pivot.position', 'medior')->groupBy('type')->all();
        $senior = $competencies->where('pivot.position', 'senior')->groupBy('type')->all();

        $requiredSkills = [
            'junior' => $junior,
            'medior' => $medior,
            'senior' => $senior,
        ];
        
        return response()->json([
            'skills' => $people->competencies->groupBy('type')->all(),
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
