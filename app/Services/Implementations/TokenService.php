<?php

namespace App\Services\Implementations;

use App\Models\User;
use App\Services\Contracts\TokenServiceInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class TokenService implements TokenServiceInterface
{
    private string $secret;
    private int $expiration;
    private string $issuer;
    
    public function __construct()
    {
        $this->secret     = config('jwt.secret');
        $this->expiration = (int) config('jwt.expiration');
        $this->issuer     = config('jwt.issuer');
    }

    public function generateToken(User $user): string
    {
        $permissions = $user->portalPermissions()
            ->whereNull('deleted_at')
            ->pluck('access_to')
            ->toArray();

        $payload = [
            'iss'         => $this->issuer,
            'sub'         => $user->id,
            'email'       => $user->email,
            'name'        => $user->name,
            'role'        => $user->role,
            'permissions' => $permissions,
            'status'      => $user->status,
            'iat'         => time(),
            'exp'         => time() + ($this->expiration * 60),
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function validateToken(string $token): object
    {
        try {
            return JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (Exception $e) {
            throw new Exception('Token inválido o expirado: ' . $e->getMessage());
        }
    }

    public function getPayload(string $token): object
    {
        return $this->validateToken($token);
    }
}