<?php

namespace App\Http\Controllers\API;

use App\Models\Competency;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\HttpFoundation\Request;

class CompetencyController extends Controller
{
    use SoftDeletes;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Competency::all());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupByType()
    {
        return response()->json(Competency::all()->groupBy('type')->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCompetencyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $competency = Competency::create($request->only(['name', 'type']));

        return response()->json($competency);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCompetencyRequest  $request
     * @param  \App\Models\Competency  $Competency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Competency $Competency)
    {
        $Competency->update($request->only(['name','type']));

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Competency  $Competency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competency $Competency)
    {
        $Competency->delete();

        return response()->json(['status' => 'success']);
    }
}
