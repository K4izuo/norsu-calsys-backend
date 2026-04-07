<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeopleController extends Controller
{
    /**
     * Display a listing of all people.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $people = People::with([
            'linkedUser:id,first_name,last_name,email',
            'linkedByUser:id,first_name,last_name',
        ])->orderBy('created_at', 'desc')->get();

        return response()->json($people);
    }

    /**
     * Store a newly created person.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'personName' => 'required|string|max:255',
        ]);

        $person = People::create($validated);

        return response()->json($person, 201);
    }

    /**
     * Update the specified person's name.
     */
    public function update(Request $request, People $person)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'personName' => 'required|string|max:255',
        ]);

        $person->update($validated);

        return response()->json($person);
    }

    /**
     * Remove the specified person.
     */
    public function destroy(Request $request, People $person)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $person->delete();

        return response()->json(['message' => 'Person deleted successfully']);
    }

    /**
     * Link a registered user to this person record (admin pairing action).
     */
    public function linkUser(Request $request, People $person)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        // Check if this user is already linked to another People record
        $alreadyLinked = People::where('userLinkId', $validated['user_id'])
            ->where('id', '!=', $person->id)
            ->exists();

        if ($alreadyLinked) {
            return response()->json([
                'message' => 'This user is already linked to another person record.'
            ], 422);
        }

        DB::transaction(function () use ($person, $validated, $user) {
            $person->update([
                'userLinkId'     => $validated['user_id'],
                'linkTimestamp'  => now(),
                'linkedByUserId' => $user->id,
            ]);
        });

        $person->load('linkedUser:id,first_name,last_name,email', 'linkedByUser:id,first_name,last_name');

        return response()->json([
            'message' => 'User linked successfully.',
            'person'  => $person,
        ]);
    }
}
