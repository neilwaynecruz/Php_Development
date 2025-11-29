<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user', function () {
    return view('user');
});

Route::get('/product', function () {
    return view('product');
});

// Route to display the form
Route::get('/schedule', [UserController::class, 'showScheduleForm']);

// Route to post/save the data
Route::post('/save-schedule', [UserController::class, 'saveSchedule']);
