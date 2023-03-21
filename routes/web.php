<?php

use Spatie\GoogleCalendar\Event;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', function () {
  return view('application');
})->name('login');

Route::get('/events', function () {
  $events = Event::get();
  dd($events);
  response()->json($events);
});

Route::get('/{vue}', [ApplicationController::class, 'index'])->where('vue', '[\/\w\.-]*')->middleware('auth:web');
