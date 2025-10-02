<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
      $fields = $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        // 'role_id' => 'required|integer',
        // 'facultyID' => 'required|string|max:255|unique:users',
        'username' => 'required|string|max:255|unique:users',
        'password' => 'required|string|min:8',
        'campus_id' => 'required|integer|max:255',
        'college_id' => 'required|integer|max:255',
        // 'course' => 'required|string|max:255',
        'degree_course_id' => 'nullable|integer|max:255',
      ]);

      // Hash the password
      $fields['password'] = Hash::make($fields['password']);

      // Create the user
      $user = User::create($fields);

      $user->load(['roles', 'campuses']);

      return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
