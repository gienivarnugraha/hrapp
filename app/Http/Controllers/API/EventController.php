<?php

namespace App\Http\Controllers\API;

use Faker\Factory;
use App\Models\Event;
use App\Models\People;
use App\Models\JobTitle;
use App\Models\Competency;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;

class EventController extends Controller
{
    /**
   * Display a listing of the resource.
   * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
   */
  public function index(Request $request)
  {
    ['start_date' => $start_date, 'end_date' => $end_date] = $request->query();

    $user = Event::with('peoples')->whereBetween('start_date', [$start_date, $end_date])->orWhereNotNull('rrule')->get();

    return response()->json(EventResource::collection($user), 200);
  }
  /**
   * Store a newly created resource in storage.
   * @param Request $request
   * @return Renderable
   */
  public function store(Request $request)
  {
    $event = new Event($request->all());

    $event->save();

    return new EventResource($event);
  }

  /**
   * Update the specified resource in storage.
   * @param Request $request
   * @param int $id
   * @return Renderable
   */
  public function update(Request $request, $eventId)
  {

    $event = Event::findOrFail($eventId);

    $event->update($request->all());

    return new EventResource($event);
  }


  /**
   * Show the specified resource.
   * @param int $id
   * @return Renderable
   */
  public function show($eventId)
  {
    $event = Event::findOrFail($eventId);

    return new EventResource($event);
  }


  /**
   * Remove the specified resource from storage.
   * @param int $id
   * @return Renderable
   */
  public function destroy($eventId)
  {
    $event = Event::findOrFail($eventId);

    $event->delete();

    return response()->json(['deleted' => true, 'event_id' => $event->getKey()]);
  }

  public function generate(Request $request){
    ['people_id' => $peopleId, 'next_position' => $nextPosition] = $request->all();
    
    $faker = Factory::create();

    $people = People::find($peopleId);

    $requiredCompetencies = JobTitle::find($people->jobTitle->id)->competencies->where('pivot.position',$nextPosition)->pluck('id')->all();

    $competencies = $people->competencies->pluck('id')->all();
    
    $diff = collect($requiredCompetencies)->diff($competencies)->all();

    $schedule = collect();

    collect($diff)->each(function($requiredCompetencyId) use ($faker, $schedule){
     
      $competency = Competency::find($requiredCompetencyId);

      [$startDate, $endDate] = $this->getDates();

      $event = Event::where('competency_id', $requiredCompetencyId)->first();

      if($event===null){
        $event = Event::create([
          'competency_id' => $requiredCompetencyId,
          'title'         => "Training {$competency->name}",
          'color'         => $faker->hexColor(),
          'start_date'    => $startDate->format('Y-m-d'),
          'start_time'    => $startDate->format('H:i:s'),
          'end_date'      => $endDate->format('Y-m-d'),
          'end_time'      => $endDate->format('H:i:s'),
        ]);
      } 

      $schedule->push([
        "competency-{$requiredCompetencyId}" => $event->fullStartDate
      ]);

    });

    return response()->json($schedule);

  }

      /**
     * Get the dates for the activity
     *
     * @return array
     */
    protected function getDates()
    {
      $faker = Factory::create();

      $startDate = $faker->dateTimeBetween('+1 weeks', '+4 weeks');
      // Round to nearest 15
      $roundedStartSeconds = round($startDate->getTimestamp() / (15 * 60)) * (15 * 60);
      $startDate->setTime(date('H', rand(32400,54000)), date('i', $roundedStartSeconds), 0);
  
      $endDate = clone $startDate;
      // Add one or zero days to end date and the add 30 minutes
      $endDate->add(new \DateInterval('P' . rand(0, 1) . 'D'));
      $endDate->add(new \DateInterval('PT30M'));
  
      return [$startDate, $endDate];
    }
}
