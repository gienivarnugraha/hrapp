<?php

namespace App\Http\Controllers\API;

use App\Models\JobTitle;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Searchable\Search;

class JobTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $jobs = JobTitle::orderBy('user_id')->with('user');

        if (!$request->user()->hasRole('ADMIN')) {
            $jobs->where('user_id', $request->user()->id);
        }

        if ($request->has('itemsPerPage')) {
            $itemsPerPage = $request->query('itemsPerPage') == -1 ? $jobs->count() : $request->query('itemsPerPage');
            $paginator = $jobs->paginate($itemsPerPage);
        } else {
            $paginator = $jobs->get();
        }

        return response()->json($paginator);
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
        $job = JobTitle::make($request->only('name'));
        $job->user_id = $request->user()->id;
        $job->save();

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
            'junior' => $jobTitle->showSkills('junior'),
            'medior' => $jobTitle->showSkills('medior'),
            'senior' => $jobTitle->showSkills('senior'),
        ];

        $jobTitle['user'] = $request->user();

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

    public function search(Request $request)
    {
        $searchResults = (new Search())
            ->registerModel(JobTitle::class, 'name')
            ->search($request->input('q'));

        return response()->json($searchResults);
    }
}

