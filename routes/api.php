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

Route::get('/verify-email', [App\Http\Controllers\EmailVerificationController::class, 'verify']);
Route::post('/resend-verification', [App\Http\Controllers\EmailVerificationController::class, 'resend'])
  ->middleware('throttle:5,1');

Route::post('/admin/store', [App\Http\Controllers\AdminController::class, 'store']);

Route::post('users/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::get('/assets/all', [App\Http\Controllers\AssetsController::class, 'index']);