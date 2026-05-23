<?php

namespace App\Services\Implementations;

use App\Models\User;
use App\Services\Contracts\ProfileServiceInterface;
use App\Services\Contracts\S3ServiceInterface;
use Exception;

class ProfileService implements ProfileServiceInterface
{
    public function __construct(
        private S3ServiceInterface $s3Service
    ) {}

    public function getProfile(int $userId): User
    {
        $user = User::whereNull('deleted_at')
            ->find($userId);

        if (!$user) {
            throw new Exception('Usuario no encontrado.');
        }

        return $user;
    }

    public function updateProfile(int $userId, array $data): User
    {
        $user = $this->getProfile($userId);

        $user->update([
            'name'  => $data['name']  ?? $user->name,
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        return $user->fresh();
    }

    public function uploadAvatar(int $userId, $file): string
    {
        $user = $this->getProfile($userId);

        // Si ya tiene avatar, eliminar el anterior de S3
        if ($user->avatar_url) {
            $this->s3Service->delete($user->avatar_url);
        }

        $url = $this->s3Service->upload($file, 'avatars');

        $user->update(['avatar_url' => $url]);

        return $url;
    }
}