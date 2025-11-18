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
      'asset_id' => 'required|integer|exists:assets,id',
      'range' => 'required|integer|min:1',
      'time_start' => 'required|date_format:H:i',
      'time_end' => 'required|date_format:H:i|after:time_start',
      'description' => 'required|string|max:1000',
      'people_tag' => 'required|string|max:500',
      'info_type' => 'required|string|in:public,private,restricted',
      'category' => 'required|string|in:academic,social,sports,other',
      'date' => 'required|date|after_or_equal:today'
    ]);

    // Add default status
    $fields['status'] = 'pending';

    // Add authenticated user if available
    if (\Illuminate\Support\Facades\Auth::check()) {
      $fields['user_id'] = \Illuminate\Support\Facades\Auth::id();
    }

    $reservation = Reservation::create($fields);


    return response()->json([
      'reservation' => $reservation,
      'message' => 'Reservation created successfully'
    ], 201);
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
