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
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'campus_id' => 'required|integer',
            'degree_course_id' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($request) {
            if ($request->has('student_id')) {
                $roleId = 1;
                $user = User::create([
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'campus_id' => $request->campus_id,
                    'degree_course_id' => $request->degree_course_id,
                ]);
                UserRoles::create([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'full_id' => $request->student_id,
                ]);
            } elseif ($request->has('faculty_id')) {
                $roleId = 2;
                $user = User::create([
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'campus_id' => $request->campus_id,
                    'degree_course_id' => $request->degree_course_id,
                ]);
                UserRoles::create([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'full_id' => $request->faculty_id,
                ]);
            }
        });

        return response()->json(['message' => 'Registration successful!']);
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
