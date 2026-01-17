<?php

namespace App\Http\Controllers;

use App\Models\OfficeUser;
use App\Models\User;
use App\Models\UserRoles;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    // Base validation
    $rules = [
      'first_name' => 'required|string|max:255',
      'middle_name' => 'nullable|string|max:255',
      'last_name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'username' => 'nullable|string|max:255|unique:users',
      'password' => 'nullable|string|min:8',
      'campus_id' => 'required|integer|exists:campuses,id',
      'degree_course_id' => 'nullable|integer|exists:degree_courses,id',
      'office_id' => 'nullable|integer|exists:offices,id',
      'assignment_id' => 'required|string|unique:user_roles,full_id',
      'role' => 'required|string|in:student,dean,staff,admin,super admin',
    ];

    $validated = $request->validate($rules);

    // Map role name to role_id (1=student, 2=dean, 3=staff)
    $roleMap = [
      'student' => 1,
      'dean' => 2,
      'staff' => 3,
      'admin' => 4,
      'super admin' => 5,
    ];

    $roleId = $roleMap[$validated['role']] ?? null;

    if (!$roleId) {
      return response()->json([
        'message' => 'Invalid role provided.',
      ], 400);
    }

    // --- START TRANSACTION ---
    $user = DB::transaction(function () use ($validated, $roleId) {
      $user = User::create([
        'first_name' => $validated['first_name'],
        'middle_name' => $validated['middle_name'] ?? null,
        'last_name'  => $validated['last_name'],
        'email'      => $validated['email'],
        'username'   => $validated['username'] ?? null,
        'password'   => isset($validated['password']) && $validated['password'] ? bcrypt($validated['password']) : null,
        'campus_id'  => $validated['campus_id'],
        'degree_course_id' => $validated['degree_course_id'] ?? null,
      ]);

      UserRoles::create([
        'user_id' => $user->id,
        'role_id' => $roleId,
        'full_id' => $validated['assignment_id'],
      ]);

      OfficeUser::create([
        'user_id' => $user->id,
        'office_id' => $validated['office_id'],
      ]);

      return $user;
    });
    // --- END TRANSACTION ---

    // Create verification token (kept for later use)
    $token = Str::random(64);
    DB::table('email_verifications')->insert([
      'user_id'    => $user->id,
      'token'      => $token,
      'created_at' => now(),
    ]);

    // NOTE: SMTP/email sending removed per request.
    // You can add mail sending later where appropriate.

    return response()->json([
      'role' => $roleId,
    ], 201);
  }


  /**
   * Display the specified resource.
   */
  public function show(Request $request)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
