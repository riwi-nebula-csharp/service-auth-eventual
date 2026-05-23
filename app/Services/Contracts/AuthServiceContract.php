<?php

namespace App\Services\Contracts;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

interface AuthServiceContract
{
    public function register(RegisterRequest $request);

    public function login(LoginRequest $request);
}
