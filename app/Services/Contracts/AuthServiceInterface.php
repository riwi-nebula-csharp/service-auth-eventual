<?php

namespace App\Services\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): array;
    public function login(array $data): array;
    public function loginWithGoogle(string $googleToken): array;
    public function forgotPassword(string $email): void;
    public function resetPassword(array $data): void;
}