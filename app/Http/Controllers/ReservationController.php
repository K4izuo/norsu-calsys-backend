<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Controllers\Controller;
use App\Models\Assets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class ReservationController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    return response()->json(
      Reservation::with([
        'reservedByUser:id,first_name,last_name',
        'approvedByUser:id,first_name,last_name',
        'declinedByUser:id,first_name,last_name'
      ])
        ->select('id', 'title_name', 'asset_id', 'range', 'time_start', 'time_end', 'description', 'people_tag', 'info_type', 'category', 'date', 'reserve_by_user', 'approved_by_user', 'declined_by_user', 'status')
        ->get()
    );
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
      'date' => 'required|date|after_or_equal:today',
      'reserve_by_user' => 'required|integer|exists:users,id'
    ]);

    // Add default status
    $fields['status'] = 'PENDING';

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
  public function show($id)
  {
    $reservationAsset = Assets::where('id', $id)->get();

    if ($reservationAsset->isEmpty()) {
      return response()->json(['message' => 'No asset found on these reservation'], 404);
    }

    return response()->json($reservationAsset);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Reservation $reservation)
  {
    try {
      $fields = $request->validate([
        'status' => 'required|string|in:PENDING,APPROVED,REJECTED',
        'approved_by_user' => 'nullable|integer|exists:users,id',
        'declined_by_user' => 'nullable|integer|exists:users,id',
      ]);

      // If approving this reservation
      if ($fields['status'] === 'APPROVED') {
        // Get the asset_id and date from the current reservation
        $assetId = $reservation->asset_id;
        $date = $reservation->date;
        $timeStart = $reservation->time_start;
        $timeEnd = $reservation->time_end;

        // Get all other PENDING reservations with the same asset and date
        $conflictingReservations = Reservation::where('id', '!=', $reservation->id)
          ->where('asset_id', $assetId)
          ->where('date', $date)
          ->where('status', 'PENDING')
          ->get();

        // Check each for time overlap and reject them
        foreach ($conflictingReservations as $conflicting) {
          $conflictStart = $conflicting->time_start;
          $conflictEnd = $conflicting->time_end;

          // Check if times overlap
          $overlaps = (
            // Case 1: Conflict starts during approved time
            ($conflictStart >= $timeStart && $conflictStart < $timeEnd) ||
            // Case 2: Conflict ends during approved time
            ($conflictEnd > $timeStart && $conflictEnd <= $timeEnd) ||
            // Case 3: Conflict wraps around approved time
            ($conflictStart <= $timeStart && $conflictEnd >= $timeEnd)
          );

          if ($overlaps) {
            $conflicting->update([
              'status' => 'REJECTED',
              'declined_by_user' => $fields['approved_by_user'] ?? null,
            ]);

            Log::info('Rejected conflicting reservation', [
              'rejected_id' => $conflicting->id,
              'approved_id' => $reservation->id
            ]);
          }
        }

        // Update the current reservation with APPROVED status
        $reservation->status = 'APPROVED';
        $reservation->approved_by_user = $fields['approved_by_user'] ?? null;
        $reservation->save();
      } elseif ($fields['status'] === 'REJECTED') {
        // Update the current reservation with REJECTED status
        $reservation->status = 'REJECTED';
        $reservation->declined_by_user = $fields['declined_by_user'] ?? null;
        $reservation->save();
      } else {
        // For PENDING or other statuses
        $reservation->status = $fields['status'];
        $reservation->save();
      }

      // Log the update
      Log::info('Reservation updated successfully', [
        'id' => $reservation->id,
        'status' => $reservation->status,
        'approved_by_user' => $reservation->approved_by_user,
        'declined_by_user' => $reservation->declined_by_user
      ]);

      // Refresh to get the latest data from database
      $reservation->refresh();

      return response()->json([
        'reservation' => $reservation,
        'message' => 'Reservation updated successfully'
      ], 200);
    } catch (ValidationException $e) {
      return response()->json([
        'message' => 'Validation failed',
        'errors' => $e->errors()
      ], 422);
    } catch (Exception $e) {
      Log::error('Reservation update error: ' . $e->getMessage());
      Log::error('Stack trace: ' . $e->getTraceAsString());

      return response()->json([
        'message' => $e->getMessage(),
        'error' => 'An error occurred while updating the reservation'
      ], 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Reservation $reservation)
  {
    //
  }
}
