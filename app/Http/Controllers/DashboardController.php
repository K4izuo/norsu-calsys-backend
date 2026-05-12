<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $now         = now();
        $today       = $now->toDateString();
        $last30Start = $now->copy()->subDays(30);
        $prev30Start = $now->copy()->subDays(60);

        if ($user->isAdmin()) {
            return response()->json($this->adminStats($today, $last30Start, $prev30Start));
        }

        return response()->json($this->userStats($user->id, $today, $last30Start, $prev30Start));
    }

    private function adminStats(string $today, $last30Start, $prev30Start): array
    {
        $totalUsers      = User::count();
        $totalEvents     = Reservation::count();
        $upcomingEvents  = Reservation::where('status', 'APPROVED')->where('date', '>=', $today)->count();
        $pendingRequests = Reservation::where('status', 'PENDING')->count();

        $eventsLast30   = Reservation::where('created_at', '>=', $last30Start)->count();
        $eventsPrev30   = Reservation::whereBetween('created_at', [$prev30Start, $last30Start])->count();
        $usersLast30    = User::where('created_at', '>=', $last30Start)->count();
        $usersPrev30    = User::whereBetween('created_at', [$prev30Start, $last30Start])->count();
        $approvedLast30 = Reservation::where('status', 'APPROVED')->where('created_at', '>=', $last30Start)->count();
        $approvedPrev30 = Reservation::where('status', 'APPROVED')->whereBetween('created_at', [$prev30Start, $last30Start])->count();
        $pendingLast30  = Reservation::where('status', 'PENDING')->where('created_at', '>=', $last30Start)->count();
        $pendingPrev30  = Reservation::where('status', 'PENDING')->whereBetween('created_at', [$prev30Start, $last30Start])->count();

        return [
            'is_admin'                => true,
            'total_users'             => $totalUsers,
            'total_users_change'      => $this->changePercent($usersLast30, $usersPrev30),
            'approved_events'         => null,
            'approved_events_change'  => null,
            'total_events'            => $totalEvents,
            'total_events_change'     => $this->changePercent($eventsLast30, $eventsPrev30),
            'upcoming_events'         => $upcomingEvents,
            'upcoming_events_change'  => $this->changePercent($approvedLast30, $approvedPrev30),
            'pending_requests'        => $pendingRequests,
            'pending_requests_change' => $this->changePercent($pendingLast30, $pendingPrev30),
            'sparklines'              => [
                'users'    => $this->weeklySparkline(new User(), 'created_at', 12),
                'events'   => $this->weeklySparkline(new Reservation(), 'created_at', 12),
                'approved' => null,
                'upcoming' => $this->weeklySparklineFiltered(
                    Reservation::where('status', 'APPROVED'), 'created_at', 12
                ),
                'pending'  => $this->weeklySparklineFiltered(
                    Reservation::where('status', 'PENDING'), 'created_at', 12
                ),
            ],
        ];
    }

    private function userStats(int $userId, string $today, $last30Start, $prev30Start): array
    {
        $base = Reservation::where('reserved_by_user', $userId);

        $totalEvents     = (clone $base)->count();
        $approvedEvents  = (clone $base)->where('status', 'APPROVED')->count();
        $upcomingEvents  = (clone $base)->where('status', 'APPROVED')->where('date', '>=', $today)->count();
        $pendingRequests = (clone $base)->where('status', 'PENDING')->count();

        $eventsLast30   = (clone $base)->where('created_at', '>=', $last30Start)->count();
        $eventsPrev30   = (clone $base)->whereBetween('created_at', [$prev30Start, $last30Start])->count();
        $approvedLast30 = (clone $base)->where('status', 'APPROVED')->where('created_at', '>=', $last30Start)->count();
        $approvedPrev30 = (clone $base)->where('status', 'APPROVED')->whereBetween('created_at', [$prev30Start, $last30Start])->count();
        $pendingLast30  = (clone $base)->where('status', 'PENDING')->where('created_at', '>=', $last30Start)->count();
        $pendingPrev30  = (clone $base)->where('status', 'PENDING')->whereBetween('created_at', [$prev30Start, $last30Start])->count();

        return [
            'is_admin'                => false,
            'total_users'             => null,
            'total_users_change'      => null,
            'approved_events'         => $approvedEvents,
            'approved_events_change'  => $this->changePercent($approvedLast30, $approvedPrev30),
            'total_events'            => $totalEvents,
            'total_events_change'     => $this->changePercent($eventsLast30, $eventsPrev30),
            'upcoming_events'         => $upcomingEvents,
            'upcoming_events_change'  => $this->changePercent($approvedLast30, $approvedPrev30),
            'pending_requests'        => $pendingRequests,
            'pending_requests_change' => $this->changePercent($pendingLast30, $pendingPrev30),
            'sparklines'              => [
                'users'    => null,
                'events'   => $this->weeklySparklineFiltered(clone $base, 'created_at', 12),
                'approved' => $this->weeklySparklineFiltered(
                    (clone $base)->where('status', 'APPROVED'), 'created_at', 12
                ),
                'upcoming' => $this->weeklySparklineFiltered(
                    (clone $base)->where('status', 'APPROVED'), 'created_at', 12
                ),
                'pending'  => $this->weeklySparklineFiltered(
                    (clone $base)->where('status', 'PENDING'), 'created_at', 12
                ),
            ],
        ];
    }

    private function changePercent(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function weeklySparkline(object $model, string $column, int $weeks): array
    {
        $results = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start     = now()->subWeeks($i + 1)->startOfWeek();
            $end       = now()->subWeeks($i)->endOfWeek();
            $results[] = $model->newQuery()->whereBetween($column, [$start, $end])->count();
        }
        return $results;
    }

    private function weeklySparklineFiltered($query, string $column, int $weeks): array
    {
        $results = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start     = now()->subWeeks($i + 1)->startOfWeek();
            $end       = now()->subWeeks($i)->endOfWeek();
            $results[] = (clone $query)->whereBetween($column, [$start, $end])->count();
        }
        return $results;
    }
}
