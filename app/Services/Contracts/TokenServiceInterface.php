<?php

namespace App\Services\Contracts;

use App\Models\User;

interface TokenServiceInterface
{
    public function generateToken(User $user): string;
    public function validateToken(string $token): object;
    public function getPayload(string $token): object;
}