<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Check if user has admin or super-admin role
        $is_admin = $user->roles()
            ->whereIn('slug', ['admin', 'super-admin'])
            ->exists();

        if (!$is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Admin access required',
            ], 403);
        }

        return $next($request);
    }
}
