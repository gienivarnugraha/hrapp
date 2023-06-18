<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class CompetencyController extends Controller
{
    use SoftDeletes;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $competency = Competency::orderBy('type');

        return response()->json([
          "data" => $competency->get(),
          "total" => $competency->count()
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupByType()
    {
        $types = Competency::all()->groupBy('type')->all();
        return response()->json($types);
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
    public function update(Request $request, Competency $competency)
    {
        $competency->update($request->only(['name','type']));

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
