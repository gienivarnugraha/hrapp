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
        return response()->json([
            'competencies' => $jobTitle->competencies->groupBy('type')->all(),
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
        $jobTitle->update($request->only(['name','competencies'])); 

        if($request->competencies){
            $competencies = collect($request->competencies)->reduce(function($prev, $next){
                $nx = collect($next)->pluck('id')->all();
                return array_merge($prev, $nx);
            },[]);
            $jobTitle->competencies()->sync($competencies);
        }

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

        return response()->json([ 'status' => 'success' ]);
    }
}
