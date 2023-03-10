<?php

use App\Models\Event;
use App\Models\People;
use App\Mail\MailNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Mail\MailNotification as MailMailNotification;

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

Route::get('/notification', function () {
  $people = People::find(1);

  return Mail::to('nivar.nugraha@gmail.org')->send(new MailNotification($people));

});

Route::get('/{vue}', [ApplicationController::class, 'index'])->where('vue', '[\/\w\.-]*')->middleware('auth:web');
