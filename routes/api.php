<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\PeopleController;
use App\Http\Controllers\API\JobTitleController;
use App\Http\Controllers\API\CompetencyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show'])->name('user.show');
    Route::get('competencies/types', [CompetencyController::class, 'groupByType']);
    Route::get('peoples/export/{id}', [PeopleController::class,'export']);
    Route::post('events/generate', [EventController::class,'generate']);
    Route::put('events/{id}/attendance', [EventController::class,'attendance']);
    Route::post('logout', [AuthController::class,'logout']);	
    Route::get('job-title/search', [JobTitleController::class,'search']);	

    Route::apiResource('job-title', JobTitleController::class);
    Route::apiResource('competencies', CompetencyController::class)->except('show');
    Route::apiResource('peoples', PeopleController::class);
    Route::apiResource('events', EventController::class);
});

