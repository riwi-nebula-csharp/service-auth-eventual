<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Responses\ApiResponse;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return ApiResponse::created([
                'token' => $result['token'],
                'user'  => new UserResource($result['user']),
            ], 'Usuario registrado exitosamente.');

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return ApiResponse::success([
                'token' => $result['token'],
                'user'  => new UserResource($result['user']),
            ], 'Login exitoso.');

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 401);
        }
    }

    public function logout(): JsonResponse
    {
        // Con JWT stateless el logout lo maneja el frontend
        // borrando el token. Aquí simplemente confirmamos.
        return ApiResponse::success(null, 'Sesión cerrada exitosamente.');
    }

    public function redirectToGoogle(): JsonResponse
    {
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return ApiResponse::success(['url' => $url]);
    }

    public function handleGoogleCallback(): \Illuminate\Http\RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            $result = $this->authService->loginWithGoogle($googleUser);

            $token = $result['token'];

            // Redirigir al frontend con el token en la URL
            return redirect()->away(
                env('FRONTEND_NEBULA_URL') . '/#/auth/callback?token=' . $token
            );

        } catch (Exception $e) {
            return redirect()->away(
                env('FRONTEND_NEBULA_URL') . '/#/login?error=' . urlencode($e->getMessage())
            );
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->forgotPassword($request->email);

            return ApiResponse::success(
                null,
                'Si el correo existe recibirás un enlace de recuperación.'
            );

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword($request->validated());

            return ApiResponse::success(null, 'Contraseña actualizada exitosamente.');

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}