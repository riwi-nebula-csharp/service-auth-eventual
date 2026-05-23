<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtHelper
{
    public static function decode(string $token): object
    {
        try {
            return JWT::decode(
                $token,
                new Key(config('jwt.secret'), 'HS256')
            );
        } catch (Exception $e) {
            throw new Exception('Token inválido o expirado.');
        }
    }

    public static function getUserId(string $token): int
    {
        return (int) self::decode($token)->sub;
    }

    public static function getRole(string $token): string
    {
        return self::decode($token)->role;
    }

    public static function getPermissions(string $token): array
    {
        return (array) self::decode($token)->permissions;
    }

    public static function hasPermission(string $token, string $permission): bool
    {
        return in_array($permission, self::getPermissions($token));
    }
}