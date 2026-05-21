<?php

namespace App\Http\Middleware;

use App\Models\UserRoles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRoleId = (int) UserRoles::where('user_id', $request->user()->id)->value('role_id');

        $allowed = array_map('intval', $roles);

        if (! in_array($userRoleId, $allowed, true)) {
            return response()->json(['message' => 'Forbidden: insufficient role'], 403);
        }

        return $next($request);
    }
}
