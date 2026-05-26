<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class InternalController extends Controller
{
    public function getUserByEmail(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->first();

            if (!$user) {
                return ApiResponse::notFound('Usuario no encontrado.');
            }

            return ApiResponse::success([
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ]);

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getUserEmailsByIds(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids'   => 'required|string',
            ]);

            $ids   = explode(',', $request->ids);
            $users = User::whereIn('id', $ids)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->get(['id', 'name', 'email']);

            return ApiResponse::success($users);

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}