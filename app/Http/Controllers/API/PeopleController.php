<?php

namespace App\Http\Controllers\API;

use App\Models\People;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PeopleCollection;
use App\Http\Resources\PeopleResource;


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

/*         $peoples->map(function ($people) {
            return $people['skills'] = $people->competencies->groupBy('type')->all();
        }); */
/* 
        $peoples->map(function ($people) {
            $skills = collect([
                ['id' => 'hard', 'type' => 'subheader', 'title' => 'Hard Skills'],
                ['type' => 'divider'],
                ['id' => 'soft', 'type' => 'subheader', 'title' => 'Soft Skills'],
                ['type' => 'divider'],
                ['id' => 'doa', 'type' => 'subheader', 'title' => 'DOA'],
            ]);

            $people->competencies->each(function ($competency) use ($skills) {
                $index = -1;

                if ($competency->type === 'hard') {
                    $index = $skills->search(function ($skill) {
                        return $skill["id"] === 'hard';
                    });
                }
                if ($competency->type === 'soft') {
                    $index = $skills->search(function ($skill) {
                        return $skill["id"] === 'soft';
                    });
                }
                if ($competency->type === 'doa') {
                    $index = $skills->search(function ($skill) {
                        return $skill["id"] === 'doa';
                    });
                }

                $skill = ['name' => $competency->name, 'value' => $competency->id];

                $skills->splice($index+1, 0, [$skill]);
            });

            return $people['skills'] = $skills;
        }); */



        return response($peoples);
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
