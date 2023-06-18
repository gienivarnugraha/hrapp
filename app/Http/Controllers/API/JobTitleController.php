<?php

namespace App\Http\Controllers\API;

use App\Enums\PositionEnum;
use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
    $jobs = JobTitle::with('user')->withCount('peoples')->orderBy('user_id');

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
      '1' => $jobTitle->showSkills(1),
      '2' => $jobTitle->showSkills(2),
      '3' => $jobTitle->showSkills(3),
    ];

    $peoples = $jobTitle->showPeoples();

    return response()->json([
      'competencies' => $skills,
      'peoples' => $peoples,
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
      '1' => $jobTitle->showSkills(1),
      '2' => $jobTitle->showSkills(2),
      '3' => $jobTitle->showSkills(3),
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

  public function getPeoplesByJobTitle($id, $position)
  {
    $posIndex = PositionEnum::from($position);

    $peoples = JobTitle::find($id)->peoples()->where('peoples.position' , '<=', $posIndex)->get();

    return response()->json($peoples);
  }

  public function search(Request $request)
  {
    $searchResults = (new Search())
      ->registerModel(JobTitle::class, 'name')
      ->search($request->input('q'));

    return response()->json($searchResults);
  }
}
