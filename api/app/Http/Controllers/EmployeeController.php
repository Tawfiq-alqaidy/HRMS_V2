<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController
{

    public function index()
    {
        $employees = Employee::with(['user:id', 'department:id,name'])->paginate(20);

        if ($employees->isEmpty()) {
            return response()->json(['message' => 'No employees found'], 404);
        }

        return EmployeeResource::collection($employees)
            ->additional([
                'meta' => [
                    'last_page' => $employees->lastPage(),
                ]
            ]);
    }


    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        // Create user
        $user = User::create([
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'] ?? 'employee',
            'isActive' => $validated['isActive'] ?? true,
        ]);

        // Prepare employee data (exclude user fields)
        $employeeData = collect($validated)
            ->except(['email', 'password', 'role'])
            ->toArray();
        $employeeData['user_id'] = $user->id;

        // Create employee
        $employee = Employee::create($employeeData);
        $employee->load(['user:id', 'department:id,name']);

        return response()->json(['message' => 'Employee created successfully.',], 201);
    }


    public function show(string $id)
    {
        $employee = Employee::with(['user:id', 'department:id,name'])->findOrFail($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return new EmployeeResource($employee);
    }



    public function update(UpdateEmployeeRequest $request, string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->validated());
        $employee->load(['user:id', 'department:id,name']);
        return response()->json(['message' => 'Employee updated successfully.'], 200);
    }

    public function archiving(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->isActive = 0;
        $employee->save();
        // Archive related user
        if ($employee->user) {
            $employee->user->isActive = 0;
            $employee->user->save();
        }
        return response()->json(['message' => 'Employee archived successfully.']);
    }

    public function restore(string $id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $employee->isActive = 1;
        $employee->save();
        // Restore related user
        if ($employee->user) {
            $employee->user->isActive = 1;
            $employee->user->save();
        }
        return response()->json(['message' => 'Employee restored successfully.'], 200);
    }

    public function archivedEmployees()
    {
        $archivedEmployees = Employee::with(['user:id', 'department:id,name'])
            ->where('isActive', 0)
            ->get();

        if ($archivedEmployees->isEmpty()) {
            return response()->json(['message' => 'No archived employees found'], 404);
        }
        return EmployeeResource::collection($archivedEmployees);
    }

    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully.'], 200);
    }
}
