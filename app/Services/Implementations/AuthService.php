<?php

namespace App\Services\Implementations;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\Contracts\AuthServiceContract;
use Firebase\JWT\JWT;

class AuthService implements AuthServiceContract
{
	public function register(RegisterRequest $request)
	{
		$data = $request->validated();

		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password_hash' => bcrypt($data['password']),
			'phone' => $data['phone'] ?? null,
			'provider' => 'local',
			'role' => 'client',
			'status' => 'active',
		]);
	}

	public function login(LoginRequest $request)
	{
		$data = $request->validated();
		$user = User::where('email', $data['email'])->first();

		if (! $user || ! $user->password_hash || ! password_verify($data['password'], $user->password_hash)) {
			abort(401, 'Invalid credentials');
		}

		if ($user->status === 'inactive') {
			abort(403, 'User is inactive');
		}

		$issuedAt = time();
		$expiresAt = $issuedAt + ((int) env('JWT_EXPIRATION', 480) * 60);
		$portalPermissions = $user->portalPermissions()
			->pluck('access_to')
			->all();

		$token = JWT::encode([
			'sub' => $user->id,
			'email' => $user->email,
			'role' => $user->role,
			'portal_permissions' => $portalPermissions,
			'iat' => $issuedAt,
			'exp' => $expiresAt,
		], env('JWT_SECRET'), 'HS256');

		return [
			'token' => $token,
			'user' => $user,
		];
	}
}
