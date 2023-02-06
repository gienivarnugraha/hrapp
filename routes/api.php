<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('job-title/all', [JobTitleController::class, 'getAll']);
    Route::get('competencies/types', [CompetencyController::class, 'groupByType']);
    Route::post('events/generate', [EventController::class,'generate']);
    Route::post('logout', [AuthController::class,'logout']);	

    Route::apiResource('job-title', JobTitleController::class);
    Route::apiResource('competencies', CompetencyController::class)->except('show');
    Route::apiResource('peoples', PeopleController::class);
    Route::apiResource('events', EventController::class);
});

