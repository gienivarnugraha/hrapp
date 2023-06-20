<?php

namespace App\Http\Controllers\API;

use App\Enums\PositionEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Mail\MailNotification;
use App\Models\Competency;
use App\Models\Event;
use App\Models\JobTitle;
use App\Models\People;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Spatie\GoogleCalendar\Event as GoogleCalendar;

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

    $gcal = GoogleCalendar::find($event->gcal_id);

    $gcal->update([
      'startDateTime' =>  Carbon::createFromDate($event->fullStartDate),
      'endDateTime' =>  Carbon::createFromDate($event->fullEndDate),
    ]);

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

  public function generate(Request $request)
  {
    ['people_id' => $peopleId, 'next_position' => $nextPosition] = $request->all();

    $people = People::find($peopleId);

    $requiredCompetencies = JobTitle::find($people->jobTitle->id)->competencies->where('pivot.position', $nextPosition)->pluck('id')->all();

    $competencies = $people->competencies->where('pivot.grade', '>=', 70)->pluck('id')->all();

    $diff = collect($requiredCompetencies)->diff($competencies)->all();

    $schedule = collect();

    collect($diff)->each(function ($requiredCompetencyId) use ($schedule, $people) {
      $faker = Factory::create();

      $competency = Competency::find($requiredCompetencyId);

      [$startDate, $endDate] = $this->getDates();

      $event = Event::where('competency_id', $requiredCompetencyId)->whereDate('start_date', '>', Carbon::now())->first();

      if ($event === null) {
        $name = "Training {$competency->name}";

        // $gcal = GoogleCalendar::create([
        //   'name' => $name,
        //   'startDateTime' => $startDate,
        //   'endDateTime' => $endDate,
        // ]);

        $event = Event::create([
          'competency_id' => $requiredCompetencyId,
          'title'         => $name,
          'description'   => $faker->sentence(),
          'color'         => $faker->safeHexColor(),
          'start_date'    => $startDate->format('Y-m-d'),
          'start_time'    => $startDate->format('H:i:s'),
          'end_date'      => $endDate->format('Y-m-d'),
          'end_time'      => $endDate->format('H:i:s'),
          // 'gcal_id'       => $gcal->googleEvent->id,
        ]);
      };

      if ($people->events()->where('event_id', $event->id)->count() == 0) {
        $people->events()->attach($event->id);
      }

      $schedule->push([
        $requiredCompetencyId => $event->fullStartDate
      ]);
    });

    // Mail::to($people->email)->send(new MailNotification($people));

    return response()->json($schedule);
  }

  public function attendance($eventId, Request $request)
  {

    $event = Event::find($eventId);

    if ($event->isDue) return response()->json(['error' => 'this training is over'], 400);

    $attendance = $request->input('attendance');

    foreach ($attendance as $attendee) {

      $people = People::find($attendee['people_id']);


      if ($attendee['attended']) {
        $people->competencies()->syncWithoutDetaching([$event->competency_id => ['grade' => (int) $attendee['grade']]]);
        $event->peoples()->syncWithoutDetaching([$people->id => ['attended' => true]]);
        // GoogleCalendar::find($event->gcal_id)->delete();
      } else {
        $people->competencies()->updateExistingPivot($event->competency_id, ['grade' => 0]);
        $event->peoples()->detach($people->id);
      }
    }

    return response()->json(['status' => 'success']);
  }


  /**
   * Get the dates for the activity
   *
   * @return array
   */
  protected function getDates()
  {
    $faker = Factory::create();

    $startDate = Carbon::createFromDate($faker->dateTimeBetween('+1 weeks', '+4 weeks'));

    if ($startDate->isWeekend()) {
      $startDate->addWeekday('+1');
    }
    // Round to nearest 15
    $roundedStartSeconds = round($startDate->getTimestamp() / (15 * 60)) * (15 * 60);
    $startDate->setTime(date('H', rand(10800, 32400)), date('i', $roundedStartSeconds), 0);

    $endDate = clone $startDate;
    // Add one or zero days to end date and the add 30 minutes
    $endDate->addWeekday('+' . rand(1, 2));
    $endDate->add(new \DateInterval('PT30M'));

    return [$startDate, $endDate];
  }
}
