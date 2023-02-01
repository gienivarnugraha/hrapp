<?php

use App\Http\Controllers\API\AuthController;
use App\Models\User;
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
    Route::apiResource('job-title', JobTitleController::class);
    Route::apiResource('competencies', CompetencyController::class);
    Route::apiResource('peoples', PeopleController::class);
    Route::apiResource('events', EventController::class);
    Route::post('events/generate', [EventController::class,'generate']);
    Route::post('logout', [AuthController::class,'logout']);	
});

