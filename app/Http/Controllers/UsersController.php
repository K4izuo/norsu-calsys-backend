<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // Check user type based on what the frontend sent
        $isStudent = $request->has('studentID');
        $isFaculty = !$isStudent; // fallback

        // Common validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'campus_id' => 'required|integer|exists:campuses,id',
            'degree_course_id' => 'nullable|integer|exists:degree_courses,id',
        ];

        // Add specific validation for each user type
        if ($isStudent) {
            $rules['studentID'] = 'required|string|max:50|unique:user_roles,full_id';
        } else {
            $rules['facultyID'] = 'required|string|max:50|unique:user_roles,full_id';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $isStudent) {
            // Create user
            $user = User::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'campus_id' => $validated['campus_id'],
                'degree_course_id' => $validated['degree_course_id'] ?? null,
            ]);

            // Assign role (1 = Student, 2 = Faculty)
            $roleId = $isStudent ? 1 : 2;
            $fullId = $isStudent ? $validated['studentID'] : $validated['facultyID'];

            UserRoles::create([
                'user_id' => $user->id,
                'role_id' => $roleId,
                'full_id' => $fullId,
            ]);
        });

        return response()->json([
            'message' => $isStudent
                ? 'Student registration successful!'
                : 'Faculty registration successful!',
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
