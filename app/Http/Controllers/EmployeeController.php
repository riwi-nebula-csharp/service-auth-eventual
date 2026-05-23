<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeCollection;
use App\Http\Resources\EmployeeResource;
use App\Responses\ApiResponse;
use App\Services\Contracts\EmployeeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeServiceInterface $employeeService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $employees = $this->employeeService->getAll();

            return ApiResponse::success(
                new EmployeeCollection($employees)
            );

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $employee = $this->employeeService->getById($id);

            return ApiResponse::success(new EmployeeResource($employee));

        } catch (Exception $e) {
            return ApiResponse::notFound($e->getMessage());
        }
    }

    public function store(CreateEmployeeRequest $request): JsonResponse
    {
        try {
            $adminId  = $request->auth_user->sub;
            $employee = $this->employeeService->create($request->validated(), $adminId);

            return ApiResponse::created(
                new EmployeeResource($employee),
                'Empleado creado exitosamente.'
            );

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(UpdateEmployeeRequest $request, int $id): JsonResponse
    {
        try {
            $adminId  = $request->auth_user->sub;
            $employee = $this->employeeService->update($id, $request->validated(), $adminId);

            return ApiResponse::success(
                new EmployeeResource($employee),
                'Empleado actualizado exitosamente.'
            );

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive',
            ]);

            $employee = $this->employeeService->updateStatus($id, $request->status);

            return ApiResponse::success(
                new EmployeeResource($employee),
                'Estado actualizado exitosamente.'
            );

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->employeeService->delete($id);

            return ApiResponse::success(null, 'Empleado eliminado exitosamente.');

        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}