<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UploadAvatarRequest;
use App\Http\Resources\UserResource;
use App\Responses\ApiResponse;
use App\Services\Contracts\ProfileServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileServiceInterface $profileService
    ) {}

    public function show(Request $request): JsonResponse
    {
        try {
            $userId = $request->attributes->get('auth_user')->sub;
            $user   = $this->profileService->getProfile($userId);

            return ApiResponse::success(new UserResource($user));

        } catch (Exception $e) {
            return ApiResponse::notFound($e->getMessage());
        }
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $userId = $request->attributes->get('auth_user')->sub;
            $user   = $this->profileService->updateProfile($userId, $request->validated());

            return ApiResponse::success(
                new UserResource($user),
                'Perfil actualizado exitosamente.'
            );

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        try {
            $userId = $request->attributes->get('auth_user')->sub;
            $url    = $this->profileService->uploadAvatar($userId, $request->file('avatar'));

            return ApiResponse::success(
                ['avatar_url' => $url],
                'Foto de perfil actualizada exitosamente.'
            );

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}