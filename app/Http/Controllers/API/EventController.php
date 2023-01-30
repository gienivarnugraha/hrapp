<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Models\People;
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
}
