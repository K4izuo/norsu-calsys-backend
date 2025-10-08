<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Users routes
Route::post('/users/store', [App\Http\Controllers\UsersController::class, 'store']);

// Campuses routes
Route::get('/campuses/all', [App\Http\Controllers\CampusesController::class, 'index']);

Route::get('/offices/all', [App\Http\Controllers\OfficesController::class, 'index']);

Route::get('/degreeCourse/{id}', [App\Http\Controllers\DegreeCoursesController::class, 'show']);