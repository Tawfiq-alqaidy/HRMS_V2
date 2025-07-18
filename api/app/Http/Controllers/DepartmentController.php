<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;

class DepartmentController extends Controller
{

    public function index()
    {
        $departments = Department::with('manager', 'employees')->withCount('employees')->get();
        if ($departments->isEmpty()) {
            return response()->json(['data' => []], 200);
        }
        return DepartmentResource::collection($departments);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->validated());
        $department->load('manager', 'employees');
        return response()->json(['message' => 'Department created successfully.'], 201);
    }


    public function show($id)
    {
        $department = Department::withCount('employees')->with('manager')->where('id', $id)->firstOrFail();
        return new DepartmentResource($department);
    }


    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = Department::where('id', $id)->firstOrFail();
        $department->update($request->validated());
        $department->load('manager', 'employees');
        return response()->json(['message' => 'Department updated successfully.'], 200);
    }


    public function destroy($id)
    {
        $department = Department::where('id', $id)->firstOrFail();
        if ($department->employees()->count() > 0  || $department == null) {
            return response()->json(['message' => 'Cannot delete department with employees.'], 400);
        }
        $department->delete();
        return response()->json(['message' => 'Department deleted successfully.']);
    }

    public function employees($id)
    {
        $department = Department::with('employees')->where('id', $id)->firstOrFail();
        return new DepartmentResource($department);
    }
}
