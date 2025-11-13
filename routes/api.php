<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UsersController,
    AdminController,
    CampusesController,
    OfficesController,
    DegreeCoursesController,
    EmailVerificationController,
    AssetsController
};

// Public routes (no authentication required)
Route::post('users/login', [AuthController::class, 'login']);
Route::post('users/store', [UsersController::class, 'store']);
Route::get('verify-email', [EmailVerificationController::class, 'verify']);
Route::post('resend-verification', [EmailVerificationController::class, 'resend'])
    ->middleware('throttle:5,1');

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::get('me', [AuthController::class, 'me']);
    
    // Campuses & Offices
    Route::get('campuses/all', [CampusesController::class, 'index']);
    Route::get('offices/all', [OfficesController::class, 'index']);
    
    // Degree Courses
    Route::get('degreeCourse/{id}', [DegreeCoursesController::class, 'show']);
    
    // Assets
    Route::get('assets/all', [AssetsController::class, 'index']);
    
    // Admin
    Route::post('admin/store', [AdminController::class, 'store']);
});