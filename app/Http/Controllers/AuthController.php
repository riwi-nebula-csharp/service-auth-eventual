<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Contracts\AuthServiceContract;

class AuthController extends Controller
{
    public function __construct(private AuthServiceContract $authService)
    {
    }

    public function ping()
    {
        return response()->json([
            'message' => 'pong desde controlador',
            'status'  => 200
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request);

        return response()->json([
            'token' => $result['token'],
            'user' => (new UserResource($result['user']))->resolve($request),
        ], 200);
    }
}
