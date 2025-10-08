<?php

namespace App\Http\Controllers;

use App\Models\DegreeCourses;
use Illuminate\Http\Request;

class DegreeCoursesController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $degreeCourses = DegreeCourses::where('office_id', $id)->get();

        if ($degreeCourses->isEmpty()) {
            return response()->json(['message' => 'No degree courses found for this office'], 404);
        }

        return response()->json($degreeCourses);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DegreeCourses $degreeCourses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DegreeCourses $degreeCourses)
    {
        //
    }
}
