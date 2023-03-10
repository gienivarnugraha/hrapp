<?php

namespace App\Http\Controllers\API;

use App\Models\People;
use App\Models\JobTitle;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Exports\PeoplesExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $peoples = People::orderBy('job_title_id')->with('jobTitle');

        if (!$request->user()->hasRole('ADMIN')) {
            $peoples->whereHas('jobTitle', function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            });
        }

        if ($request->has('filterBy')) {
            $peoples->where('job_title_id', $request->query('filterBy'));
        }
        

        $itemsPerPage = $request->query('itemsPerPage') == -1 ? $peoples->count() : $request->query('itemsPerPage');

        $paginator = $peoples->paginate($itemsPerPage);

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
        $people =  People::create($request->only(['name', 'nik', 'org', 'position', 'job_title_id']));

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
        $jobs = JobTitle::find($people->job_title_id);

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
        $people->update($request->only(['name', 'nik', 'org', 'position']));

        if ($request->skills) {
            $forSync = collect($request->skills)->map(function ($value, $index) use ($request) {
                $sync = collect($request->skills[$index])->reduce(function ($prev, $next) {
                    $prev[] = $next['id'];
                    return $prev;
                }, []);

                return $sync;
            });

            $flatten = Arr::flatten($forSync, 1);

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

    public function export($id)
    {
        ob_end_clean(); // this
        ob_start(); // and this

        Excel::store(new PeoplesExport($id), 'export_store.xls', 'public');
        return Excel::download(new PeoplesExport($id), 'export_download.xls', \Maatwebsite\Excel\Excel::XLS);
    }
}
