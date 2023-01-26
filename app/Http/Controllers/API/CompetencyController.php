<?php

namespace App\Http\Controllers\API;

use App\Models\Competency;
use App\Http\Requests\StoreCompetencyRequest;
use App\Http\Requests\UpdateCompetencyRequest;

class CompetencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCompetencyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompetencyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Competency  $Competency
     * @return \Illuminate\Http\Response
     */
    public function show(Competency $Competency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Competency  $Competency
     * @return \Illuminate\Http\Response
     */
    public function edit(Competency $Competency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCompetencyRequest  $request
     * @param  \App\Models\Competency  $Competency
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompetencyRequest $request, Competency $Competency)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Competency  $Competency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competency $Competency)
    {
        //
    }
}
