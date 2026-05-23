<?php

namespace App\Services\Implementations;

use App\Models\User;
use App\Models\PasswordResetToken;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\TokenServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private TokenServiceInterface $tokenService
    ) {}

    public function register(array $data): array
    {
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'phone'         => $data['phone'] ?? null,
            'provider'      => 'local',
            'role'          => 'client',
            'status'        => 'active',
        ]);

        $token = $this->tokenService->generateToken($user);

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            throw new Exception('Credenciales inválidas.');
        }

        if (!Hash::check($data['password'], $user->password_hash)) {
            throw new Exception('Credenciales inválidas.');
        }

        if ($user->status === 'inactive') {
            throw new Exception('Cuenta inactiva. Contacta al administrador.');
        }

        $token = $this->tokenService->generateToken($user);

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }

    public function loginWithGoogle(string $googleToken): array
    {
        $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')
            ->stateless()
            ->userFromToken($googleToken);

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'        => $googleUser->getName(),
                'provider'    => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar_url'  => $googleUser->getAvatar(),
                'role'        => 'client',
                'status'      => 'active',
            ]
        );

        if ($user->status === 'inactive') {
            throw new Exception('Cuenta inactiva. Contacta al administrador.');
        }

        $token = $this->tokenService->generateToken($user);

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }

    public function forgotPassword(string $email): void
    {
        $user = User::where('email', $email)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            return; // respuesta genérica, no revelar si el email existe
        }

        // Invalidar tokens anteriores
        PasswordResetToken::where('user_id', $user->id)
            ->where('used', false)
            ->update(['used' => true]);

        $token = Str::random(64);

        PasswordResetToken::create([
            'user_id'    => $user->id,
            'token_hash' => Hash::make($token),
            'expires_at' => now()->addMinutes(60),
            'used'       => false,
        ]);

        // TODO: publicar evento a n8n para enviar correo
        // con el token de recuperación
    }

    public function resetPassword(array $data): void
    {
        $tokens = PasswordResetToken::where('used', false)
            ->where('expires_at', '>', now())
            ->get();

        $resetToken = null;
        foreach ($tokens as $t) {
            if (Hash::check($data['token'], $t->token_hash)) {
                $resetToken = $t;
                break;
            }
        }

        if (!$resetToken) {
            throw new Exception('Token inválido o expirado.');
        }

        $user = User::find($resetToken->user_id);
        $user->update(['password_hash' => Hash::make($data['password'])]);

        $resetToken->update(['used' => true]);
    }
}