<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JwtTelescopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = Auth::user();
            // Kiểm tra điều kiện người dùng có quyền truy cập
            if (!$user || !$this->canAccessTelescope($user)) {
                return response()->json(['error' => 'Access denied!'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 401);
        }

        return $next($request);
    }

    private function canAccessTelescope($user)
    {
        // Chỉ cho phép người dùng có email cụ thể hoặc role 'admin'
        return in_array($user->email, [
            'test@example.com',
        ]);
    }
}
