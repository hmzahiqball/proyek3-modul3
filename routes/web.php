<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
 
Route::get('/', function () {
    return view('welcome');
});

Route::get('/activities', [ActivityController::class, 'index'])
 ->name('activities.index');
Route::get('/activities/{activity}', [ActivityController::class, 'show'])
 ->name('activities.show');
