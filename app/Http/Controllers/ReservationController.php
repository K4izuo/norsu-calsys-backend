<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservationController extends Controller
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
          'title_name' => 'required|string|max:255',
          'asset' => 'required|string|max:255',
          'range' => 'required|integer',
          'time_start' => 'required|date',
          'time_end' => 'required|date',
          'description' => 'required|string|max:255',
          'people_tag' => 'required|string|max:255',
          'info_type' => 'required|string|max:255',
          'category' => 'required|string|max:255',
          'date' => 'required|date'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}
