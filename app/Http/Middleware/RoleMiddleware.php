<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // Super Admin memiliki hak akses ke rute admin maupun super_admin
        if ($user->role === 'super_admin' && (in_array('admin', $roles) || in_array('super_admin', $roles))) {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
