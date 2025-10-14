<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function store(Request $request)
    {
        $fields = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'adminID' => 'required|string|max:50|unique:user_roles,full_id',
        ]);

        $admin = DB::transaction(function () use ($fields) {
            $user = User::create([
                'first_name' => $fields['first_name'],
                'middle_name' => $fields['middle_name'] ?? null,
                'last_name'  => $fields['last_name'],
                'email'      => $fields['email'],
            ]);

            UserRoles::create([
                'user_id' => $user->id,
                'role_id' => 4, // 4 = ADMIN (adjust if your admin role_id is different)
                'full_id' => $fields['adminID'],
            ]);

            return $user;
        });

        return response()->json([
            'message' => 'Admin registration successful!',
            'admin' => $admin,
        ], 201);
    }
}
