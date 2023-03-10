<?php

namespace App\Http\Controllers\API;

use App\Models\Competency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;

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

        if( $request->has('itemsPerPage') ) {
            $itemsPerPage = $request->query('itemsPerPage') == -1 ? $competency->count() : $request->query('itemsPerPage');
            
            $paginator = $competency->paginate($itemsPerPage);
        } else {
            $paginator = $competency->get();
        }

        return response($paginator);
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
