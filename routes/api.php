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
  ReservationController,
  PeopleController,
  NotificationsController
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
Route::get('users/{id}', [UsersController::class, 'show'])->where('id', '[0-9]+');


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
  // Auth
  Route::post('logout', [AuthController::class, 'logout']);
  Route::get('me', [AuthController::class, 'me']);
  Route::put('update-password', [AuthController::class, 'updatePassword']);

  // Event Reservation
  Route::post('event/reservation', [ReservationController::class, 'store']);
  Route::get('reservations/{id}', [ReservationController::class, 'show']);
  Route::put('reservations/{reservation}', [ReservationController::class, 'update']);
  Route::put('reservations/{reservation}/move', [ReservationController::class, 'move']);

  // Users
  Route::get('users/all', [UsersController::class, 'index']);
  Route::put('users/{id}', [UsersController::class, 'update'])->where('id', '[0-9]+');
  Route::delete('users/{id}', [UsersController::class, 'destroy'])->where('id', '[0-9]+');

  // Assets - Move this here
  Route::get('assets/all', [AssetsController::class, 'index']);
  Route::post('assets/store', [AssetsController::class, 'store']);
  Route::get('assets/{id}', [AssetsController::class, 'show']);
  Route::put('assets/{id}', [AssetsController::class, 'update']);
  Route::delete('assets/{id}', [AssetsController::class, 'destroy']);
  // Route::get('offices/all', [OfficesController::class, 'index']);

  Route::post('/update-token-expiration', [AuthController::class, 'updateTokenExpiration']);

  // People
  Route::get('people', [PeopleController::class, 'index']);
  Route::post('people', [PeopleController::class, 'store']);
  Route::put('people/{person}', [PeopleController::class, 'update']);
  Route::delete('people/{person}', [PeopleController::class, 'destroy']);
  Route::post('people/{person}/link-user', [PeopleController::class, 'linkUser']);

  // Notifications
  Route::get('notifications', [NotificationsController::class, 'index']);
  Route::get('notifications/unread-count', [NotificationsController::class, 'unreadCount']);
  Route::put('notifications/{id}/read', [NotificationsController::class, 'markRead']);
  Route::put('notifications/mark-all-read', [NotificationsController::class, 'markAllRead']);

});
