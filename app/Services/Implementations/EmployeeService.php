<?php

namespace App\Services\Implementations;

use App\Models\User;
use App\Models\PortalPermission;
use App\Services\Contracts\EmployeeServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class EmployeeService implements EmployeeServiceInterface
{
    public function getAll(): Collection
    {
        return User::with(['portalPermissions' => function ($query) {
                $query->whereNull('deleted_at');
            }])
            ->where('role', 'employee')
            ->whereNull('deleted_at')
            ->get();
    }

    public function getById(int $id): User
    {
        $employee = User::with(['portalPermissions' => function ($query) {
                $query->whereNull('deleted_at');
            }])
            ->where('role', 'employee')
            ->whereNull('deleted_at')
            ->find($id);

        if (!$employee) {
            throw new Exception('Empleado no encontrado.');
        }

        return $employee;
    }

    public function create(array $data, int $adminId): User
    {
        return DB::transaction(function () use ($data, $adminId) {
            $employee = User::create([
                'name'          => $data['name'],
                'email'         => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'phone'         => $data['phone'] ?? null,
                'provider'      => 'local',
                'role'          => 'employee',
                'status'        => 'active',
            ]);

            foreach ($data['permissions'] as $permission) {
                PortalPermission::create([
                    'user_id'    => $employee->id,
                    'access_to'  => $permission,
                    'granted_by' => $adminId,
                ]);
            }

            return $employee->load(['portalPermissions' => function ($query) {
                $query->whereNull('deleted_at');
            }]);
        });
    }

    public function update(int $id, array $data, int $adminId): User
    {
        return DB::transaction(function () use ($id, $data, $adminId) {
            $employee = $this->getById($id);

            $employee->update([
                'name'  => $data['name']  ?? $employee->name,
                'phone' => $data['phone'] ?? $employee->phone,
            ]);

            if (isset($data['permissions'])) {
                // Eliminar permisos anteriores
                PortalPermission::where('user_id', $employee->id)->forceDelete();

                // Crear nuevos permisos
                foreach ($data['permissions'] as $permission) {
                    PortalPermission::create([
                        'user_id'    => $employee->id,
                        'access_to'  => $permission,
                        'granted_by' => $adminId,
                    ]);
                }
            }

            return $employee->fresh()->load(['portalPermissions' => function ($query) {
                $query->whereNull('deleted_at');
            }]);
        });
    }

    public function updateStatus(int $id, string $status): User
    {
        $employee = $this->getById($id);
        $employee->update(['status' => $status]);
        return $employee->fresh();
    }

    public function delete(int $id): void
    {
        $employee = $this->getById($id);
        $employee->update(['deleted_at' => now()]);
    }
}