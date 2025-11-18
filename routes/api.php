<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UsersController,
    AdminController,
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

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::get('me', [AuthController::class, 'me']);
    
    // Resources
    Route::get('campuses/all', [CampusesController::class, 'index']);
    Route::get('offices/all', [OfficesController::class, 'index']);
    Route::get('degreeCourse/{id}', [DegreeCoursesController::class, 'show']);
    Route::get('assets/all', [AssetsController::class, 'index']);
    
    // Admin
    Route::post('admin/store', [AdminController::class, 'store']);

    // Event Reservation
    Route::post('event/reservation', [ReservationController::class, 'store']);
});