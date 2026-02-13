<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
  AuthController,
  UsersController,
  // AdminController,
  CampusesController,
  OfficesController,
  DegreeCoursesController,
  EmailVerificationController,
  AssetsController,
  ReservationController
};

// Public routes
Route::post('users/login', [AuthController::class, 'login']);
Route::post('users/store', [UsersController::class, 'store']);
Route::get('verify-email', [EmailVerificationController::class, 'verify']);
Route::post('resend-verification', [EmailVerificationController::class, 'resend'])
  ->middleware('throttle:5,1');
Route::get('campuses/all', [CampusesController::class, 'index']);
Route::get('offices/all', [OfficesController::class, 'listAllOffices']);
Route::get('degreeCourse/{id}', [DegreeCoursesController::class, 'show']);
// Route::get('assets/all', [AssetsController::class, 'index']);
Route::get('reservations/all', [ReservationController::class, 'index']);
Route::get('reservations/assets/{id}', [ReservationController::class, 'show']);
Route::get('users/{id}', [UsersController::class, 'update']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
  // Auth
  Route::post('logout', [AuthController::class, 'logout']);
  Route::get('me', [AuthController::class, 'me']);

  // Event Reservation
  Route::post('event/reservation', [ReservationController::class, 'store']);
  Route::get('reservations/{id}', [ReservationController::class, 'show']);
  Route::put('reservations/{reservation}', [ReservationController::class, 'update']);

  // Assets - Move this here
  Route::get('assets/all', [AssetsController::class, 'index']);
  Route::post('assets/store', [AssetsController::class, 'store']);
  Route::get('assets/{id}', [AssetsController::class, 'show']);
  Route::put('assets/{id}', [AssetsController::class, 'update']);
  Route::delete('assets/{id}', [AssetsController::class, 'destroy']);
  // Route::get('offices/all', [OfficesController::class, 'index']);

  Route::post('/update-token-expiration', [AuthController::class, 'updateTokenExpiration']);
});
