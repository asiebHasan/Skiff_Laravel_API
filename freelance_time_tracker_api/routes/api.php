<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeLogController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // client api
    Route::get('client/', [ClientController::class, 'index']);
    Route::get('client/{id}', [ClientController::class, 'show']);
    Route::post('client/', [ClientController::class, 'store']);
    Route::post('client/{id}', [ClientController::class, 'update']);
    Route::delete('client/{id}', [ClientController::class, 'delete']);

    // project
    Route::get('project/', [ProjectController::class, 'index']);
    Route::get('project/{id}', [ProjectController::class, 'show']);
    Route::post('project/', [ProjectController::class, 'store']);
    Route::post('project/{id}', [ProjectController::class, 'update']);
    Route::delete('project/{id}', [ProjectController::class, 'destroy']);

    // timelog
    Route::get('/time-logs/{id}', [TimeLogController::class, 'show']);
    Route::post('/time-logs', [TimeLogController::class, 'store']);
    Route::post('/time-logs/{id}', [TimeLogController::class, 'update']);
    Route::delete('/time-logs/{id}', [TimeLogController::class, 'destroy']);

    // Start & End logging
    Route::post('/time-logs/{id}/start', [TimeLogController::class, 'start_time_logs']);
    Route::post('/time-logs/{id}/end', [TimeLogController::class, 'end_time_logs']);

    // Logs by filters
    Route::get('/time-logs/logs/project/{id}', [TimeLogController::class, 'logsByProject']);
    Route::get('/time-logs/logs/user', [TimeLogController::class, 'logsByUser']);
    Route::get('/time-logs/logs/day', [TimeLogController::class, 'logsByDay']);
    Route::get('/time-logs/logs/week', [TimeLogController::class, 'logsByWeek']);
    Route::get('/time-logs/logs/between', [TimeLogController::class, 'logsBetweenDates']);


    // Total hours
    Route::get('/time-logs/total/project/{id}', [TimeLogController::class, 'totalHoursByProject']);
    Route::post('/time-logs/total/day', [TimeLogController::class, 'totalHoursByDay']);
    Route::get('/time-logs/total/client/{client_id}', [TimeLogController::class, 'totalHoursByClient']);

    // export PDF
    Route::post('/time-logs/export/pdf', [TimeLogController::class, 'exportSelectedLogsToPDF']);

});