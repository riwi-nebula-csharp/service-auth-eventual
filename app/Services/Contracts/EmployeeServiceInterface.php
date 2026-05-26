<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeServiceInterface
{
    public function getAll(): Collection;
    public function getById(int $id): User;
    public function create(array $data, int $adminId): User;
    public function update(int $id, array $data, int $adminId): User;
    public function updateStatus(int $id, string $status): User;
    public function delete(int $id): void;
}