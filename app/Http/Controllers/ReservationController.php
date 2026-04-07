<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\Tagging;
use App\Http\Controllers\Controller;
use App\Models\Assets;
use App\Models\User;
use App\Notifications\TaggedInApprovedEvent;
use App\Notifications\ReservationSubmittedNotification;
use App\Notifications\ReservationApprovedNotification;
use App\Notifications\ReservationDeclinedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        'declinedByUser:id,first_name,last_name',
        'latestStatus:id,reservation_id,move_reason'
      ])
        ->select('id', 'title_name', 'asset_id', 'range', 'time_start', 'time_end', 'description', 'people_tag', 'info_type', 'category', 'date', 'original_date', 'reserved_by_user', 'approved_by_user', 'declined_by_user', 'status', 'is_moved')
        ->get()
    );
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'title_name'          => 'required|string|max:255',
      'asset_id'            => 'required|integer|exists:assets,id',
      'range'               => 'required|integer|min:1',
      'time_start'          => 'required|date_format:H:i',
      'time_end'            => 'required|date_format:H:i|after:time_start',
      'description'         => 'required|string|max:1000',
      'people_tag'          => 'nullable|string|max:500',
      'info_type'           => 'required|string|in:public,private,restricted',
      'category'            => 'required|string|in:academic,social,sports,other',
      'date'                => 'required|date|after_or_equal:today',
      'reserved_by_user'    => 'required|integer|exists:users,id',
      'tagged_people_ids'   => 'nullable|array',
      'tagged_people_ids.*' => 'integer|exists:people,id',
    ]);

    $taggedPeopleIds = $validated['tagged_people_ids'] ?? [];
    unset($validated['tagged_people_ids']);

    $validated['status'] = 'PENDING';

    if (\Illuminate\Support\Facades\Auth::check()) {
      $validated['user_id'] = \Illuminate\Support\Facades\Auth::id();
    }

    $reservation = DB::transaction(function () use ($validated, $taggedPeopleIds) {
      $reservation = Reservation::create($validated);

      foreach ($taggedPeopleIds as $personId) {
        Tagging::firstOrCreate([
          'tagPeopleID'         => $personId,
          'taggedReservationID' => $reservation->id,
        ]);
      }

      return $reservation;
    });

    // Notify all admins about the new pending reservation (exclude the submitter)
    $admins = User::whereHas('userRole', fn($q) => $q->where('role_id', 3))
      ->where('id', '!=', $reservation->reserved_by_user)
      ->get();

    foreach ($admins as $admin) {
      $admin->notify(new ReservationSubmittedNotification($reservation));
    }

    return response()->json([
      'reservation' => $reservation,
      'message'     => 'Reservation created successfully'
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
        'status' => 'required|string|in:PENDING,APPROVED,DECLINED',
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
              'status' => 'DECLINED',
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

        // Notify the person who submitted the reservation
        $submitter = User::find($reservation->reserved_by_user);
        if ($submitter) {
          $submitter->notify(new ReservationApprovedNotification($reservation));
        }

        // Notify tagged people who have a linked user account
        $taggedWithLink = $reservation->taggings()
          ->with('person.linkedUser')
          ->get()
          ->filter(fn($t) => $t->person?->linkedUser !== null);

        foreach ($taggedWithLink as $tagging) {
          $tagging->person->linkedUser->notify(new TaggedInApprovedEvent($reservation));
        }
      } elseif ($fields['status'] === 'DECLINED') {
        // Update the current reservation with DECLINED status
        $reservation->status = 'DECLINED';
        $reservation->declined_by_user = $fields['declined_by_user'] ?? null;
        $reservation->save();

        // Notify the person who submitted the reservation
        $submitter = User::find($reservation->reserved_by_user);
        if ($submitter) {
          $submitter->notify(new ReservationDeclinedNotification($reservation));
        }
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
   * Move an approved reservation to a new date/time.
   */
  public function move(Request $request, Reservation $reservation)
  {
    try {
      if ($reservation->status !== 'APPROVED') {
        return response()->json(['message' => 'Only approved reservations can be moved.'], 422);
      }

      $fields = $request->validate([
        'new_date'       => 'required|date|after_or_equal:today',
        'new_time_start' => 'required|date_format:H:i',
        'new_time_end'   => 'required|date_format:H:i|after:new_time_start',
        'move_reason'    => 'required|string|max:1000',
        'moved_by'       => 'required|integer|exists:users,id',
      ]);

      $newStart = $fields['new_time_start'];
      $newEnd   = $fields['new_time_end'];

      // Check for conflicts against APPROVED reservations on same asset + new date
      $conflicts = Reservation::where('id', '!=', $reservation->id)
        ->where('asset_id', $reservation->asset_id)
        ->where('date', $fields['new_date'])
        ->where('status', 'APPROVED')
        ->get();

      foreach ($conflicts as $conflict) {
        $overlaps = (
          ($conflict->time_start >= $newStart && $conflict->time_start < $newEnd) ||
          ($conflict->time_end   >  $newStart && $conflict->time_end  <= $newEnd) ||
          ($conflict->time_start <= $newStart && $conflict->time_end  >= $newEnd)
        );
        if ($overlaps) {
          return response()->json([
            'message'  => 'Conflict: another approved reservation exists at the new time slot.',
            'conflict' => $conflict,
          ], 409);
        }
      }

      // Log the move to reservation_statuses for audit trail
      ReservationStatus::create([
        'reservation_id' => $reservation->id,
        'moved_by_user'  => $fields['moved_by'],
        'move_reason'    => $fields['move_reason'],
        'old_date'       => $reservation->date,
        'old_time_start' => $reservation->time_start,
        'old_time_end'   => $reservation->time_end,
        'new_date'       => $fields['new_date'],
        'new_time_start' => $fields['new_time_start'],
        'new_time_end'   => $fields['new_time_end'],
      ]);

      // Apply the move — always track the most recent date moved FROM
      $reservation->update([
        'date'          => $fields['new_date'],
        'time_start'    => $fields['new_time_start'],
        'time_end'      => $fields['new_time_end'],
        'is_moved'      => true,
        'original_date' => $reservation->date,
      ]);

      $reservation->refresh();

      Log::info('Reservation moved', [
        'id'       => $reservation->id,
        'moved_by' => $fields['moved_by'],
        'new_date' => $fields['new_date'],
      ]);

      return response()->json([
        'reservation' => $reservation,
        'message'     => 'Reservation moved successfully',
      ], 200);

    } catch (ValidationException $e) {
      return response()->json([
        'message' => 'Validation failed',
        'errors'  => $e->errors(),
      ], 422);
    } catch (Exception $e) {
      Log::error('Reservation move error: ' . $e->getMessage());
      return response()->json(['message' => 'An error occurred while moving the reservation.'], 500);
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
