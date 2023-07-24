<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Users\ScheduleController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// auth
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
//users
    // schedule
    Route::post('/addschedule', [ScheduleController::class, 'addschedule']);
    Route::post('/updateschedule', [ScheduleController::class, 'updateschedule']);
    Route::post('/allschedules', [ScheduleController::class, 'allschedules']);
    Route::post('/deleteschedule', [ScheduleController::class, 'deleteschedule']);
    // Documents
    Route::post('/uploaddocuments', [RegisterController::class, 'uploadDocuments']);

    //tester



