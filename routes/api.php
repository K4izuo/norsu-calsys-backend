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
  NotificationsController,
  ActivityLogController,
  DashboardController
};

// Public routes — auth-sensitive (tight limits)
Route::post('users/login', [AuthController::class, 'login'])
  ->middleware('throttle:10,1');
Route::get('verify-email', [EmailVerificationController::class, 'verify'])
  ->middleware('throttle:10,1');
Route::post('resend-verification', [EmailVerificationController::class, 'resend'])
  ->middleware('throttle:5,1');

// Public routes — general read-only (60/min per IP)
Route::middleware('throttle:60,1')->group(function () {
  Route::get('campuses/all', [CampusesController::class, 'index']);
  Route::get('offices/all', [OfficesController::class, 'listAllOffices']);
  Route::get('degreeCourse/all', [DegreeCoursesController::class, 'index']);
  Route::get('degreeCourse/{id}', [DegreeCoursesController::class, 'show']);
  Route::get('reservations/all', [ReservationController::class, 'index']);
  Route::get('reservations/assets/{id}', [ReservationController::class, 'show']);
});

// Protected routes — 120/min per user (keyed by user ID via named 'api' limiter)
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
  // Auth
  Route::post('logout', [AuthController::class, 'logout']);
  Route::get('me', [AuthController::class, 'me']);
  Route::put('update-password', [AuthController::class, 'updatePassword'])
    ->middleware('throttle:5,1');
  Route::post('session/touch', [AuthController::class, 'touchSession']);

  // Reservations
  Route::post('event/reservation', [ReservationController::class, 'store'])
    ->middleware('throttle:20,1');
  Route::get('reservations/internal', [ReservationController::class, 'accountIndex']);
  Route::get('reservations/queue', [ReservationController::class, 'queue']);
  Route::get('reservations/{id}', [ReservationController::class, 'show']);
  Route::put('reservations/{reservation}', [ReservationController::class, 'update'])
    ->middleware('role:1,3,5,6,7,8,9,10,11,12');
  Route::put('reservations/{reservation}/equipment', [ReservationController::class, 'updateEquipment']);
  Route::put('reservations/{reservation}/multimedia-comment', [ReservationController::class, 'updateMultimediaComment']);
  Route::put('reservations/{reservation}/move', [ReservationController::class, 'move'])
    ->middleware('role:5,12');
  Route::post('reservations/{reservation}/resubmit', [ReservationController::class, 'resubmit']);

  // Users (admin-only registration + management)
  Route::post('users/store', [UsersController::class, 'store'])
    ->middleware(['role:3', 'throttle:5,1']);
  Route::get('users/all', [UsersController::class, 'index'])
    ->middleware('role:3');
  Route::get('users/{id}', [UsersController::class, 'show'])
    ->where('id', '[0-9]+')
    ->middleware('role:3');
  Route::put('users/{id}', [UsersController::class, 'update'])
    ->where('id', '[0-9]+')
    ->middleware('role:3');
  Route::delete('users/{id}', [UsersController::class, 'destroy'])
    ->where('id', '[0-9]+')
    ->middleware('role:3');

  // Assets (admin manages writes)
  Route::get('assets/all', [AssetsController::class, 'index']);
  Route::post('assets/store', [AssetsController::class, 'store'])
    ->middleware('role:3');
  Route::get('assets/{id}', [AssetsController::class, 'show']);
  Route::put('assets/{id}', [AssetsController::class, 'update'])
    ->middleware('role:3');
  Route::delete('assets/{id}', [AssetsController::class, 'destroy'])
    ->middleware('role:3');

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

  // Activity Logs
  Route::get('activity-logs', [ActivityLogController::class, 'index'])
    ->middleware('throttle:30,1');

  // Dashboard (open to all authenticated users)
  Route::get('dashboard/stats', [DashboardController::class, 'stats'])
    ->middleware('throttle:30,1');
});
