<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationApproval;
use App\Models\ReservationStatus;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->id;
        $logs = collect();

        $reservations = Reservation::where('reserved_by_user', $userId)
            ->with('asset:id,asset_name')
            ->select('id', 'title_name', 'asset_id', 'created_at')
            ->latest()
            ->limit(100)
            ->get();

        foreach ($reservations as $reservation) {
            $assetName = $reservation->asset?->asset_name ?? 'Unknown venue';
            $logs->push([
                'id'          => 'reservation_created_' . $reservation->id,
                'type'        => 'reservation_created',
                'title'       => 'Created reservation request',
                'description' => "{$reservation->title_name} — {$assetName}",
                'created_at'  => $reservation->created_at->toIso8601String(),
            ]);
        }

        $approvals = ReservationApproval::where('user_id', $userId)
            ->with('reservation:id,title_name,asset_id', 'reservation.asset:id,asset_name')
            ->latest()
            ->limit(100)
            ->get();

        foreach ($approvals as $approval) {
            $type = match ($approval->action) {
                'APPROVED' => 'reservation_approved',
                'DECLINED' => 'reservation_declined',
                'ENDORSE'  => 'reservation_endorsed',
                default    => 'reservation_approved',
            };

            $title = match ($approval->action) {
                'APPROVED' => 'Approved reservation',
                'DECLINED' => 'Declined reservation',
                'ENDORSE'  => 'Endorsed reservation',
                default    => 'Actioned reservation',
            };

            $reservationTitle = $approval->reservation?->title_name ?? 'Unknown event';
            $assetName        = $approval->reservation?->asset?->asset_name ?? 'Unknown venue';

            $logs->push([
                'id'          => 'approval_' . $approval->id,
                'type'        => $type,
                'title'       => $title,
                'description' => "{$reservationTitle} — {$assetName}",
                'created_at'  => $approval->created_at->toIso8601String(),
            ]);
        }

        $moves = ReservationStatus::where('moved_by_user', $userId)
            ->with('reservation:id,title_name,asset_id', 'reservation.asset:id,asset_name')
            ->latest()
            ->limit(100)
            ->get();

        foreach ($moves as $move) {
            $reservationTitle = $move->reservation?->title_name ?? 'Unknown event';
            $assetName        = $move->reservation?->asset?->asset_name ?? 'Unknown venue';

            $logs->push([
                'id'          => 'move_' . $move->id,
                'type'        => 'reservation_moved',
                'title'       => 'Rescheduled reservation',
                'description' => "{$reservationTitle} — {$assetName}",
                'created_at'  => $move->created_at->toIso8601String(),
            ]);
        }

        return response()->json(
            $logs->sortByDesc('created_at')->values()->take(100)
        );
    }
}
