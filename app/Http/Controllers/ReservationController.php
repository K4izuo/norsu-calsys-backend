<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationApproval;
use App\Models\ReservationStatus;
use App\Models\Tagging;
use App\Http\Controllers\Controller;
use App\Models\Assets;
use App\Models\User;
use App\Notifications\TaggedInApprovedEvent;
use App\Notifications\ReservationSubmittedNotification;
use App\Notifications\ReservationApprovedNotification;
use App\Notifications\ReservationDeclinedNotification;
use App\Notifications\ReservationStageAdvancedNotification;
use App\Notifications\ReservationStageDeclinedNotification;
use App\Notifications\ReservationFullyApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;

class ReservationController extends Controller
{
  public function index()
  {
    return response()->json($this->reservationsIndexQuery(false)->get());
  }

  public function accountIndex()
  {
    return response()->json($this->reservationsIndexQuery(true)->get());
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'title_name'           => 'required|string|max:255',
      'asset_id'             => 'required|integer|exists:assets,id',
      'range'                => 'required|integer|min:1',
      'time_start'           => 'required|date_format:H:i',
      'time_end'             => 'required|date_format:H:i|after:time_start',
      'description'          => 'required|string|max:1000',
      'people_tag'           => 'nullable|string|max:500',
      'info_type'            => 'required|string|in:public,private,restricted',
      'category'             => 'required|string|in:academic,social,sports,other',
      'other_category'       => 'nullable|string|max:255|required_if:category,other',
      'date'                 => 'required|date|after_or_equal:today',
      'reserved_by_user'     => 'required|integer|exists:users,id',
      'tagged_people_ids'    => 'nullable|array',
      'tagged_people_ids.*'  => 'integer|exists:people,id',
      'equipment'            => 'nullable|array',
      'equipment.*.name'     => 'required_with:equipment|string|max:255',
      'equipment.*.quantity' => 'required_with:equipment|integer|min:1',
      'outsource'            => 'nullable|string|max:2000',
      'guests'               => 'nullable|array',
      'guests.*.name'        => 'required_with:guests|string|max:255',
      'guests.*.details'     => 'nullable|string|max:500',
      'involves_students'    => 'nullable|boolean',
      'requires_vpaa'        => 'nullable|boolean',
      'requires_vpsas'       => 'nullable|boolean',
      'requires_vpaf'        => 'nullable|boolean',
      'requires_vprde'       => 'nullable|boolean',
      'requestor_type'       => 'nullable|string|in:student,faculty,office',
      'student_sub_type'     => 'nullable|string|in:student_org,csg,lso,sgdc',
      'student_org_name'     => 'nullable|string|max:255',
      'csg_name'             => 'nullable|string|max:255',
      'requestor_tagged'     => 'nullable|array',
      'requestor_tagged.*.id' => 'nullable|integer',
      'requestor_tagged.*.name' => 'required_with:requestor_tagged|string|max:255',
      'proof_of_request'     => 'nullable|string|max:2000',
      'proof_of_approval'    => 'nullable|string|max:2000',
    ]);

    $taggedPeopleIds = $validated['tagged_people_ids'] ?? [];
    $equipmentItems  = $validated['equipment'] ?? [];
    $guestItems      = $validated['guests'] ?? [];
    unset($validated['tagged_people_ids'], $validated['equipment'], $validated['guests']);

    $validated['status'] = 'PENDING';

    if (\Illuminate\Support\Facades\Auth::check()) {
      $validated['user_id'] = \Illuminate\Support\Facades\Auth::id();
    }

    $reservation = DB::transaction(function () use ($validated, $taggedPeopleIds, $equipmentItems, $guestItems) {
      $reservation = Reservation::create($validated);

      foreach ($taggedPeopleIds as $personId) {
        Tagging::firstOrCreate([
          'tagPeopleID'         => $personId,
          'taggedReservationID' => $reservation->id,
        ]);
      }

      foreach ($equipmentItems as $item) {
        $reservation->equipment()->create([
          'name'     => $item['name'],
          'quantity' => $item['quantity'],
        ]);
      }

      foreach ($guestItems as $guest) {
        $reservation->guests()->create([
          'name'    => $guest['name'],
          'details' => $guest['details'] ?? null,
        ]);
      }

      return $reservation;
    });

    $user = User::find($validated['reserved_by_user']);

    if ($user->isAdmin()) {
      return response()->json(['message' => 'Admin accounts cannot create reservations.'], 403);

    } elseif ($user->isUniversityPresident()) {
      $reservation->update(['current_stage' => 'university_president']);

    } elseif ($user->isDean()) {
      $reservation->update(['requires_vpaa' => true]);
      $stage = ($validated['involves_students'] ?? false) ? 'student_director' : 'vp_approval';
      $reservation->update(['current_stage' => $stage]);

    } elseif ($user->isHeadOfOffice()) {
      $office = $user->office;
      if ($office?->oversight_vp_id) {
        $oversightVp = User::find($office->oversight_vp_id);
        $oversightRoleId = $oversightVp?->getRoleId();
        match ($oversightRoleId) {
          6 => $reservation->update(['requires_vpaa'  => true]),
          7 => $reservation->update(['requires_vpsas' => true]),
          8 => $reservation->update(['requires_vpaf'  => true]),
          9 => $reservation->update(['requires_vprde' => true]),
          default => null,
        };
      }
      $r = $reservation->fresh();
      $hasVP = $r->requires_vpaa || $r->requires_vpsas || $r->requires_vpaf || $r->requires_vprde;

      if ($validated['involves_students'] ?? false) {
        $stage = 'student_director';
      } elseif ($hasVP) {
        $stage = 'vp_approval';
      } else {
        $stage = 'campus_director';
      }
      $reservation->update(['current_stage' => $stage]);

    } else {
      // Campus Director and others
      $hasVP = ($validated['requires_vpaa']  ?? false) || ($validated['requires_vpsas'] ?? false)
            || ($validated['requires_vpaf']  ?? false) || ($validated['requires_vprde'] ?? false);

      if ($validated['involves_students'] ?? false) {
        $stage = 'student_director';
      } elseif ($hasVP) {
        $stage = 'vp_approval';
      } else {
        $stage = 'campus_director';
      }
      $reservation->update(['current_stage' => $stage]);
    }

    $fresh = $reservation->fresh();
    $this->notifyStageApprovers($fresh, $fresh->current_stage);

    return response()->json([
      'reservation' => $fresh,
      'message'     => 'Reservation created successfully',
    ], 201);
  }

  public function show($id)
  {
    $reservationAsset = Assets::where('id', $id)->get();

    if ($reservationAsset->isEmpty()) {
      return response()->json(['message' => 'No asset found on these reservation'], 404);
    }

    return response()->json($reservationAsset);
  }

  public function update(Request $request, Reservation $reservation)
  {
    try {
      $fields = $request->validate([
        'action'           => 'required|string|in:APPROVED,DECLINED,APPROVE,ENDORSE',
        'approved_by_user' => 'nullable|integer|exists:users,id',
        'declined_by_user' => 'nullable|integer|exists:users,id',
        'reason'           => [
          Rule::requiredIf($request->action === 'DECLINED'),
          'nullable',
          'string',
          'min:10',
          'max:2000',
        ],
      ]);

      $action = $fields['action'];
      $userId = $fields['approved_by_user'] ?? $fields['declined_by_user'] ?? $request->user()?->id;
      $reason = $fields['reason'] ?? null;
      $stage  = $reservation->current_stage;

      if ($action === 'DECLINED') {
        $approver        = User::find($userId);
        $declinedAtStage = $stage;

        if ($stage === 'vp_approval' && $approver) {
          $declinedAtStage = match ($approver->getRoleId()) {
            6 => 'vpaa',
            7 => 'vpsas',
            8 => 'vpaf',
            9 => 'vprde',
            default => 'vp_approval',
          };
        }

        ReservationApproval::create([
          'reservation_id' => $reservation->id,
          'stage'          => $declinedAtStage,
          'user_id'        => $userId,
          'action'         => 'DECLINED',
          'reason'         => $reason,
        ]);

        $reservation->update([
          'status'            => 'DECLINED',
          'declined_by_user'  => $userId,
          'declined_at_stage' => $declinedAtStage,
          'current_stage'     => 'declined',
        ]);

        $submitter = User::find($reservation->reserved_by_user);
        if ($submitter) {
          $submitter->notify(new ReservationStageDeclinedNotification($reservation, $declinedAtStage, $reason));
        }

        if ($stage === 'vp_approval') {
          $this->notifyOtherPendingVPs($reservation, $userId, $declinedAtStage);
        }

      } elseif ($action === 'APPROVED' && $stage === 'student_director') {
        ReservationApproval::create([
          'reservation_id' => $reservation->id,
          'stage'          => 'student_director',
          'user_id'        => $userId,
          'action'         => 'APPROVED',
          'reason'         => $reason,
        ]);

        $reservation->update(['current_stage' => 'vp_approval']);

        $this->notifyRequiredVPs($reservation);

        $submitter = User::find($reservation->reserved_by_user);
        if ($submitter) {
          $submitter->notify(new ReservationStageAdvancedNotification($reservation, 'student_director', 'vp_approval', $reason));
        }

      } elseif ($action === 'APPROVED' && $stage === 'vp_approval') {
        $approver = User::find($userId);
        $vpTarget = $this->getVPApprovalTarget($approver?->getRoleId());

        if (!$vpTarget) {
          return response()->json([
            'message' => 'Only VP accounts can approve this stage.',
          ], 422);
        }

        $vpStage = $vpTarget['stage'];
        $vpRequiredFlag = $vpTarget['flag'];

        if (!$reservation->{$vpRequiredFlag}) {
          return response()->json([
            'message' => "This reservation does not require {$vpStage} approval.",
          ], 422);
        }

        $alreadyApproved = ReservationApproval::where('reservation_id', $reservation->id)
          ->where('stage', $vpStage)
          ->where('action', 'APPROVED')
          ->exists();

        if ($alreadyApproved) {
          return response()->json([
            'message' => "This reservation has already been approved by {$vpStage}.",
          ], 409);
        }

        ReservationApproval::create([
          'reservation_id' => $reservation->id,
          'stage'          => $vpStage,
          'user_id'        => $userId,
          'action'         => 'APPROVED',
          'reason'         => $reason,
        ]);

        if ($this->allVPsApproved($reservation)) {
          $reservation->update(['current_stage' => 'campus_director']);

          $campusDirectors = User::whereHas('userRole', fn($q) => $q->where('role_id', 5))->get();
          foreach ($campusDirectors as $cd) {
            $cd->notify(new ReservationStageAdvancedNotification($reservation, 'vp_approval', 'campus_director', $reason));
          }

          $submitter = User::find($reservation->reserved_by_user);
          if ($submitter) {
            $submitter->notify(new ReservationStageAdvancedNotification($reservation, 'vp_approval', 'campus_director', $reason));
          }
        }

      } elseif ($action === 'APPROVE' && $stage === 'campus_director') {
        ReservationApproval::create([
          'reservation_id' => $reservation->id,
          'stage'          => 'campus_director',
          'user_id'        => $userId,
          'action'         => 'APPROVE',
          'reason'         => $reason,
        ]);

        $this->rejectConflictingReservations($reservation, $userId);

        $reservation->update([
          'campus_director_action' => 'approve',
          'current_stage'          => 'approved',
          'status'                 => 'APPROVED',
          'approved_by_user'       => $userId,
        ]);

        $submitter = User::find($reservation->reserved_by_user);
        if ($submitter) {
          $submitter->notify(new ReservationFullyApprovedNotification($reservation, $reason));
        }

        $this->notifyTaggedPeople($reservation);

      } elseif ($action === 'ENDORSE' && $stage === 'campus_director') {
        ReservationApproval::create([
          'reservation_id' => $reservation->id,
          'stage'          => 'campus_director',
          'user_id'        => $userId,
          'action'         => 'ENDORSE',
          'reason'         => $reason,
        ]);

        $reservation->update([
          'campus_director_action' => 'endorse',
          'current_stage'          => 'university_president',
        ]);

        $presidents = User::whereHas('userRole', fn($q) => $q->where('role_id', 12))->get();
        foreach ($presidents as $president) {
          $president->notify(new ReservationStageAdvancedNotification($reservation, 'campus_director', 'university_president', $reason));
        }

        $submitter = User::find($reservation->reserved_by_user);
        if ($submitter) {
          $submitter->notify(new ReservationStageAdvancedNotification($reservation, 'campus_director', 'university_president', $reason));
        }

      } elseif ($action === 'APPROVED' && $stage === 'university_president') {
        ReservationApproval::create([
          'reservation_id' => $reservation->id,
          'stage'          => 'university_president',
          'user_id'        => $userId,
          'action'         => 'APPROVED',
          'reason'         => $reason,
        ]);

        $this->rejectConflictingReservations($reservation, $userId);

        $reservation->update([
          'current_stage'    => 'approved',
          'status'           => 'APPROVED',
          'approved_by_user' => $userId,
        ]);

        $submitter = User::find($reservation->reserved_by_user);
        if ($submitter) {
          $submitter->notify(new ReservationFullyApprovedNotification($reservation, $reason));
        }

        $this->notifyTaggedPeople($reservation);

      } else {
        return response()->json([
          'message' => "Invalid action '{$action}' for stage '{$stage}'",
        ], 422);
      }

      Log::info('Reservation updated', [
        'id'     => $reservation->id,
        'action' => $action,
        'stage'  => $stage,
        'user'   => $userId,
      ]);

      $reservation->refresh();

      return response()->json([
        'reservation' => $reservation,
        'message'     => 'Reservation updated successfully',
      ], 200);

    } catch (ValidationException $e) {
      return response()->json([
        'message' => 'Validation failed',
        'errors'  => $e->errors(),
      ], 422);
    } catch (Exception $e) {
      Log::error('Reservation update error: ' . $e->getMessage());
      Log::error($e->getTraceAsString());
      return response()->json(['message' => $e->getMessage()], 500);
    }
  }

  public function resubmit(Request $request, Reservation $reservation)
  {
    $user = $request->user();

    if ((int) $reservation->reserved_by_user !== (int) $user->id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    if ($reservation->status !== 'DECLINED') {
      return response()->json(['message' => 'Only declined reservations can be resubmitted'], 422);
    }

    $validated = $request->validate([
      'title_name'           => 'sometimes|string|max:255',
      'asset_id'             => 'sometimes|integer|exists:assets,id',
      'range'                => 'sometimes|integer|min:1',
      'time_start'           => 'sometimes|date_format:H:i',
      'time_end'             => 'sometimes|date_format:H:i|after:time_start',
      'description'          => 'sometimes|string|max:1000',
      'people_tag'           => 'nullable|string|max:500',
      'info_type'            => 'sometimes|string|in:public,private,restricted',
      'category'             => 'sometimes|string|in:academic,social,sports,other',
      'other_category'       => 'nullable|string|max:255',
      'date'                 => 'sometimes|date|after_or_equal:today',
      'tagged_people_ids'    => 'nullable|array',
      'tagged_people_ids.*'  => 'integer|exists:people,id',
      'equipment'            => 'nullable|array',
      'equipment.*.name'     => 'required_with:equipment|string|max:255',
      'equipment.*.quantity' => 'required_with:equipment|integer|min:1',
      'outsource'            => 'nullable|string|max:2000',
      'guests'               => 'nullable|array',
      'guests.*.name'        => 'required_with:guests|string|max:255',
      'guests.*.details'     => 'nullable|string|max:500',
      'involves_students'    => 'sometimes|boolean',
      'requires_vpaa'        => 'sometimes|boolean',
      'requires_vpsas'       => 'sometimes|boolean',
      'requires_vpaf'        => 'sometimes|boolean',
      'requires_vprde'       => 'sometimes|boolean',
      'requestor_type'       => 'nullable|string|in:student,faculty,office',
      'student_sub_type'     => 'nullable|string|in:student_org,csg,lso,sgdc',
      'student_org_name'     => 'nullable|string|max:255',
      'csg_name'             => 'nullable|string|max:255',
      'requestor_tagged'     => 'nullable|array',
      'requestor_tagged.*.id' => 'nullable|integer',
      'requestor_tagged.*.name' => 'required_with:requestor_tagged|string|max:255',
      'proof_of_request'     => 'nullable|string|max:2000',
      'proof_of_approval'    => 'nullable|string|max:2000',
    ]);

    $targetStage = $reservation->declined_at_stage ?? $reservation->current_stage;

    // VP-specific stage names ('vpsas', 'vpaa', etc.) must map back to 'vp_approval'
    // so the VP queue filter (which checks current_stage = 'vp_approval') picks this up
    $currentStage = in_array($targetStage, ['vpaa', 'vpsas', 'vpaf', 'vprde'])
      ? 'vp_approval'
      : $targetStage;

    // Delete only the declined stage's approval; earlier approvals are preserved
    $reservation->approvals()->where('stage', $targetStage)->delete();

    $taggedPeopleIds = $validated['tagged_people_ids'] ?? null;
    $equipmentItems  = $validated['equipment'] ?? null;
    $guestItems      = $validated['guests'] ?? null;
    unset($validated['tagged_people_ids'], $validated['equipment'], $validated['guests']);

    DB::transaction(function () use ($reservation, $validated, $currentStage, $taggedPeopleIds, $equipmentItems, $guestItems) {
      $reservation->update(array_merge($validated, [
        'status'            => 'PENDING',
        'current_stage'     => $currentStage,
        'declined_at_stage' => null,
        'declined_by_user'  => null,
      ]));

      if ($taggedPeopleIds !== null) {
        $reservation->taggings()->delete();
        foreach ($taggedPeopleIds as $personId) {
          Tagging::firstOrCreate([
            'tagPeopleID'         => $personId,
            'taggedReservationID' => $reservation->id,
          ]);
        }
      }

      if ($equipmentItems !== null) {
        $reservation->equipment()->delete();
        foreach ($equipmentItems as $item) {
          $reservation->equipment()->create([
            'name'     => $item['name'],
            'quantity' => $item['quantity'],
          ]);
        }
      }

      if ($guestItems !== null) {
        $reservation->guests()->delete();
        foreach ($guestItems as $guest) {
          $reservation->guests()->create([
            'name'    => $guest['name'],
            'details' => $guest['details'] ?? null,
          ]);
        }
      }
    });

    $fresh = $reservation->fresh();
    $this->notifyStageApprovers($fresh, $targetStage);

    return response()->json([
      'reservation' => $fresh,
      'message'     => 'Reservation resubmitted successfully',
    ], 200);
  }

  public function queue(Request $request)
  {
    $user   = $request->user();
    $roleId = $user->getRoleId();
    $vpTarget = $this->getVPApprovalTarget($roleId);

    $query = Reservation::with([
      'reservedByUser:id,first_name,last_name',
      'approvedByUser:id,first_name,last_name',
      'declinedByUser:id,first_name,last_name',
      'equipment:id,reservation_id,name,quantity,note',
      'guests:id,reservation_id,name,details',
      'latestStatus:id,reservation_id,move_reason',
      'approvals:id,reservation_id,stage,user_id,action,reason,created_at',
      'approvals.user:id,first_name,last_name',
    ]);

    if ($vpTarget) {
      $query
        ->where('current_stage', 'vp_approval')
        ->where($vpTarget['flag'], true)
        ->whereDoesntHave('approvals', fn($q) => $q
          ->where('stage', $vpTarget['stage'])
          ->where('action', 'APPROVED'));
    } else {
      match ($roleId) {
        1, 10 => $query->where('reserved_by_user', $user->id),
        4     => $query->where('current_stage', 'student_director'),
        5     => $query->where(fn($q) => $q->where('current_stage', 'campus_director')->orWhere('reserved_by_user', $user->id)),
        11    => $query->whereRaw('1 = 1'),
        12    => $query->where(fn($q) => $q->where('current_stage', 'university_president')->orWhere('reserved_by_user', $user->id)),
        default => $query->whereRaw('1 = 0'),
      };
    }

    return response()->json($query->get());
  }

  public function updateEquipment(Request $request, Reservation $reservation)
  {
    $user = $request->user();

    if (!$user?->isMultimedia()) {
      return response()->json(['message' => 'Only multimedia accounts can update equipment.'], 403);
    }

    if ($reservation->status !== 'PENDING') {
      return response()->json(['message' => 'Equipment can only be updated while the reservation is pending.'], 422);
    }

    $fields = $request->validate([
      'multimedia_comment'  => 'nullable|string|max:2000',
      'equipment'           => 'present|array',
      'equipment.*.id'      => 'required|integer|exists:reservation_equipment,id',
      'equipment.*.quantity' => 'required|integer|min:1',
      'equipment.*.note'    => 'nullable|string|max:1000',
    ]);

    $currentEquipment = $reservation->equipment()->get()->keyBy('id');
    $currentIds = $currentEquipment->keys()->sort()->values()->all();
    $submittedIds = collect($fields['equipment'])->pluck('id')->sort()->values()->all();

    if ($currentIds !== $submittedIds) {
      return response()->json(['message' => 'Only existing equipment items can be edited.'], 422);
    }

    DB::transaction(function () use ($reservation, $fields, $currentEquipment, $request) {
      if ($request->has('multimedia_comment')) {
        $reservation->update(['multimedia_comment' => $fields['multimedia_comment'] ?? null]);
      }

      foreach ($fields['equipment'] as $item) {
        $currentEquipment[(int) $item['id']]->update([
          'quantity' => $item['quantity'],
          'note'     => $item['note'] ?? null,
        ]);
      }
    });

    $reservation->refresh()->load([
      'reservedByUser:id,first_name,last_name',
      'approvedByUser:id,first_name,last_name',
      'declinedByUser:id,first_name,last_name',
      'latestStatus:id,reservation_id,move_reason',
      'equipment:id,reservation_id,name,quantity,note',
      'guests:id,reservation_id,name,details',
      'approvals:id,reservation_id,stage,user_id,action,reason,created_at',
      'approvals.user:id,first_name,last_name',
    ]);

    return response()->json([
      'reservation' => $reservation,
      'message' => 'Equipment updated successfully',
    ]);
  }

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
      return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
    } catch (Exception $e) {
      Log::error('Reservation move error: ' . $e->getMessage());
      return response()->json(['message' => 'An error occurred while moving the reservation.'], 500);
    }
  }

  public function updateMultimediaComment(Request $request, Reservation $reservation)
  {
    $user = $request->user();

    if (!$user?->isMultimedia()) {
      return response()->json(['message' => 'Only multimedia accounts can update equipment remarks.'], 403);
    }

    if ($reservation->status !== 'PENDING') {
      return response()->json(['message' => 'Equipment remarks can only be updated while the reservation is pending.'], 422);
    }

    $fields = $request->validate(['multimedia_comment' => 'nullable|string|max:2000']);
    $reservation->update(['multimedia_comment' => $fields['multimedia_comment']]);
    return response()->json(['message' => 'Comment saved']);
  }

  public function destroy(Reservation $reservation)
  {
    //
  }

  // ── Private helpers ──────────────────────────────────────────────────────

  private function reservationsIndexQuery(bool $includeInternalEquipmentNotes)
  {
    $columns = [
      'id', 'title_name', 'asset_id', 'range', 'time_start', 'time_end',
      'description', 'people_tag', 'info_type', 'category', 'other_category',
      'outsource', 'date', 'original_date', 'reserved_by_user', 'approved_by_user',
      'declined_by_user', 'status', 'is_moved', 'involves_students', 'requires_vpaa',
      'requires_vpsas', 'requires_vpaf', 'requires_vprde', 'current_stage',
      'declined_at_stage', 'campus_director_action',
      'requestor_type', 'student_sub_type', 'student_org_name', 'csg_name',
      'requestor_tagged', 'proof_of_request', 'proof_of_approval',
      'created_at', 'updated_at',
    ];

    if ($includeInternalEquipmentNotes) {
      $columns[] = 'multimedia_comment';
    }

    return Reservation::with([
      'reservedByUser:id,first_name,last_name',
      'approvedByUser:id,first_name,last_name',
      'declinedByUser:id,first_name,last_name',
      'latestStatus:id,reservation_id,move_reason',
      $includeInternalEquipmentNotes
        ? 'equipment:id,reservation_id,name,quantity,note'
        : 'equipment:id,reservation_id,name,quantity',
      'guests:id,reservation_id,name,details',
      'approvals:id,reservation_id,stage,user_id,action,reason,created_at',
      'approvals.user:id,first_name,last_name',
    ])->select($columns);
  }

  private function notifyStageApprovers(Reservation $reservation, string $stage): void
  {
    $users = match ($stage) {
      'student_director'     => User::whereHas('userRole', fn($q) => $q->where('role_id', 4))->get(),
      'vp_approval'          => $this->getRequiredVPUsers($reservation),
      'campus_director'      => User::whereHas('userRole', fn($q) => $q->where('role_id', 5))->get(),
      'university_president' => User::whereHas('userRole', fn($q) => $q->where('role_id', 12))->get(),
      default                => User::whereRaw('0=1')->get(),
    };

    foreach ($users as $user) {
      $user->notify(new ReservationSubmittedNotification($reservation, $stage));
    }
  }

  private function notifyRequiredVPs(Reservation $reservation): void
  {
    foreach ($this->getRequiredVPUsers($reservation) as $vp) {
      $vp->notify(new ReservationSubmittedNotification($reservation, 'vp_approval'));
    }
  }

  private function getRequiredVPUsers(Reservation $reservation): \Illuminate\Database\Eloquent\Collection
  {
    $roleIds = [];
    if ($reservation->requires_vpaa)  $roleIds[] = 6;
    if ($reservation->requires_vpsas) $roleIds[] = 7;
    if ($reservation->requires_vpaf)  $roleIds[] = 8;
    if ($reservation->requires_vprde) $roleIds[] = 9;

    if (empty($roleIds)) return User::whereRaw('0=1')->get();

    return User::whereHas('userRole', fn($q) => $q->whereIn('role_id', $roleIds))->get();
  }

  private function getVPApprovalTarget(?int $roleId): ?array
  {
    return match ($roleId) {
      6 => ['stage' => 'vpaa',  'flag' => 'requires_vpaa'],
      7 => ['stage' => 'vpsas', 'flag' => 'requires_vpsas'],
      8 => ['stage' => 'vpaf',  'flag' => 'requires_vpaf'],
      9 => ['stage' => 'vprde', 'flag' => 'requires_vprde'],
      default => null,
    };
  }

  private function allVPsApproved(Reservation $reservation): bool
  {
    $approvedStages = $reservation->approvals()
      ->where('action', 'APPROVED')
      ->pluck('stage')
      ->toArray();

    if ($reservation->requires_vpaa  && !in_array('vpaa',  $approvedStages)) return false;
    if ($reservation->requires_vpsas && !in_array('vpsas', $approvedStages)) return false;
    if ($reservation->requires_vpaf  && !in_array('vpaf',  $approvedStages)) return false;
    if ($reservation->requires_vprde && !in_array('vprde', $approvedStages)) return false;

    return true;
  }

  private function notifyOtherPendingVPs(Reservation $reservation, int $decliningUserId, string $declinedStage): void
  {
    $approvedStages = $reservation->approvals()
      ->where('action', 'APPROVED')
      ->pluck('stage')
      ->toArray();

    $pendingRoleIds = [];
    if ($reservation->requires_vpaa  && !in_array('vpaa',  $approvedStages) && $declinedStage !== 'vpaa')  $pendingRoleIds[] = 6;
    if ($reservation->requires_vpsas && !in_array('vpsas', $approvedStages) && $declinedStage !== 'vpsas') $pendingRoleIds[] = 7;
    if ($reservation->requires_vpaf  && !in_array('vpaf',  $approvedStages) && $declinedStage !== 'vpaf')  $pendingRoleIds[] = 8;
    if ($reservation->requires_vprde && !in_array('vprde', $approvedStages) && $declinedStage !== 'vprde') $pendingRoleIds[] = 9;

    if (empty($pendingRoleIds)) return;

    $pendingVPs = User::whereHas('userRole', fn($q) => $q->whereIn('role_id', $pendingRoleIds))
      ->where('id', '!=', $decliningUserId)
      ->get();

    foreach ($pendingVPs as $vp) {
      $vp->notify(new ReservationStageDeclinedNotification($reservation, $declinedStage, null));
    }
  }

  private function rejectConflictingReservations(Reservation $reservation, int $approvedByUserId): void
  {
    $conflicting = Reservation::where('id', '!=', $reservation->id)
      ->where('asset_id', $reservation->asset_id)
      ->where('date', $reservation->date)
      ->where('status', 'PENDING')
      ->get();

    foreach ($conflicting as $conflict) {
      $overlaps = (
        ($conflict->time_start >= $reservation->time_start && $conflict->time_start < $reservation->time_end) ||
        ($conflict->time_end   >  $reservation->time_start && $conflict->time_end  <= $reservation->time_end) ||
        ($conflict->time_start <= $reservation->time_start && $conflict->time_end  >= $reservation->time_end)
      );

      if ($overlaps) {
        $conflict->update([
          'status'           => 'DECLINED',
          'declined_by_user' => $approvedByUserId,
          'current_stage'    => 'declined',
        ]);

        Log::info('Rejected conflicting reservation', [
          'rejected_id' => $conflict->id,
          'approved_id' => $reservation->id,
        ]);
      }
    }
  }

  private function notifyTaggedPeople(Reservation $reservation): void
  {
    $tagged = $reservation->taggings()
      ->with('person.linkedUser')
      ->get()
      ->filter(fn($t) => $t->person?->linkedUser !== null);

    foreach ($tagged as $tagging) {
      $tagging->person->linkedUser->notify(new TaggedInApprovedEvent($reservation));
    }
  }
}
