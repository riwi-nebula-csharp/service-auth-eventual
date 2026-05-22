<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\TokenServiceInterface;
use App\Services\Contracts\ProfileServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\S3ServiceInterface;
use App\Services\Implementations\AuthService;
use App\Services\Implementations\TokenService;
use App\Services\Implementations\ProfileService;
use App\Services\Implementations\EmployeeService;
use App\Services\Implementations\S3Service;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(TokenServiceInterface::class, TokenService::class);
        $this->app->bind(ProfileServiceInterface::class, ProfileService::class);
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
        $this->app->bind(S3ServiceInterface::class, S3Service::class);
    }

    public function boot(): void
    {
        //
    }
}