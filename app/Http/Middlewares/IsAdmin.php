<?php

namespace App\Http\Middleware;

use App\Helpers\JwtHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token requerido.'
                ], 401);
            }

            $payload = JwtHelper::decode($token);

            if ($payload->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado. Se requiere rol admin.'
                ], 403);
            }

            if ($payload->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuenta inactiva.'
                ], 403);
            }

            $request->merge(['auth_user' => $payload]);

            return $next($request);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado.'
            ], 401);
        }
    }
}