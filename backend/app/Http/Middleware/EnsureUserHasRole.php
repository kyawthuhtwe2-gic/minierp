<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Usage: role:admin  or  role:admin,manager
     * Role is read fresh from the database, never from the token.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $current = User::query()->whereKey($user->getAuthIdentifier())->value('role');

        $allowed = array_map(fn (string $r) => mb_strtolower(trim($r)), $roles);

        if ($current === null || ! in_array(mb_strtolower(trim($current)), $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'You do not have permission.'], 403);
        }

        return $next($request);
    }
}
