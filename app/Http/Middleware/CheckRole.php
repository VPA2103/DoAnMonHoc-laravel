<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$vaiTros): Response
    {
        $user = $request->user(); // JWT auth user

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!in_array($user->vai_tro, $vaiTros)) {
            return response()->json([
                'message' => 'Forbidden - Không có quyền truy cập'
            ], 403);
        }

        return $next($request);
    }
}