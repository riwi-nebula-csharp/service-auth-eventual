<?php

namespace App\Services\Contracts;

use App\Models\User;

interface ProfileServiceInterface
{
    public function getProfile(int $userId): User;
    public function updateProfile(int $userId, array $data): User;
    public function uploadAvatar(int $userId, $file): string;
}