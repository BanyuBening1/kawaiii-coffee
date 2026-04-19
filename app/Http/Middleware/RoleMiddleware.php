<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Comma-separated or multiple role parameters
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Get user's role name
        $userRole = $user->role?->roles_name;
        if (!$userRole) {
            return response()->json([
                'message' => 'User role not found.'
            ], 403);
        }

        // Flatten roles array and split by comma for flexibility
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
        }
        $allowedRoles = array_map('trim', $allowedRoles);

        // Check if user role matches any allowed role
        if (!in_array($userRole, $allowedRoles, true)) {
            return response()->json([
                'message' => "Forbidden. Required role(s): " . implode(', ', $allowedRoles)
            ], 403);
        }

        return $next($request);
    }
}