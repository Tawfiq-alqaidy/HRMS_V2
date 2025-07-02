<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AttendanceResource;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController
{
    public function ping()
    {
        return response()->json(['message' => 'Attendance service is running'], 200);
    }

    public function index()
    {
        $attendance = Attendance::with('employee')->get();
        if ($attendance->isEmpty()) {
            return response()->json(['message' => 'No attendance records found'], 404);
        }
        return AttendanceResource::collection($attendance);
    }

    public function startWorkDay()
    {
        $today = Carbon::now()->format('Y-m-d');

        // Check if work day already started
        if (Attendance::where('date', $today)->exists()) {
            return response()->json(['message' => 'Work day already started.',], 400);
        }

        $employees = Employee::all();
        if ($employees->isEmpty()) {
            return response()->json(['message' => 'No employees found.',], 404);
        }

        DB::beginTransaction();
        try {
            $attendance = $employees->map(function ($employee) use ($today) {
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today
                ]);
                return [
                    'EmployeeID' => $employee->EmployeeID,
                    'EmployeeName' => $employee->FullName,
                    'AttendanceDate' => $today
                ];
            });

            DB::commit();

            return response()->json(['message' => 'Started work day successfully.',], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to start work day.',
                'error'   => app()->environment('production') ? null : $e->getMessage()
            ], 500);
        }
    }




    public function show(string $id)
    {
        $attendance = Attendance::with('employee')->find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Attendance record not found'], 404);
        }
        return new AttendanceResource($attendance);
    }


    public function update(UpdateAttendanceRequest $request, string $id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Attendance record not found'], 404);
        }

        $attendance->update($request->validated());
        return response()->json(['message' => 'Attendance record updated successfully',], 200);
    }


    public function destroy(string $id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Attendance record not found'], 404);
        }

        $attendance->delete();
        return response()->json(['message' => 'Attendance record deleted successfully'], 200);
    }
}
