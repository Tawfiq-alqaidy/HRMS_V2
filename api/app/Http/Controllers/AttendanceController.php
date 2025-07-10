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

    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $employeeId = $request->query('employee_id');

        $query = Attendance::with('employee')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                // Validate date formats
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                    abort(response()->json([
                        'message' => 'Invalid date format. Please use YYYY-MM-DD format for start_date and end_date.'
                    ], 422));
                }
                $q->whereBetween('date', [$startDate, $endDate]);
            })
            ->when($employeeId, function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            });

        $attendance = $query->orderBy('date', 'desc')->paginate(20);

        $data = AttendanceResource::collection($attendance)->resolve();
        return response()->json([
            'data' => $data,
            'last_page' => $attendance->lastPage(),
        ]);
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

    public function checkIn(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');
        $employeeId = $request->input('employee_id');

        if (!$employeeId) {
            return response()->json(['message' => 'Employee ID is required.'], 400);
        }

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No attendance record found for today. Please contact administrator.'], 404);
        }

        if ($attendance->check_in_time) {
            return response()->json(['message' => 'Already checked in for today.'], 400);
        }

        $attendance->update(['check_in_time' => now()]);

        return response()->json([
            'message' => 'Check-in successful.',
        ], 200);
    }

    public function checkOut(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');
        $employeeId = $request->input('employee_id');

        if (!$employeeId) {
            return response()->json(['message' => 'Employee ID is required.'], 400);
        }

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No attendance record found for today. Please contact administrator.'], 404);
        }

        if ($attendance->check_out_time) {
            return response()->json(['message' => 'Already checked out for today.'], 400);
        }

        $attendance->update(['check_out_time' => now()]);

        return response()->json([
            'message' => 'Check-out successful.',
        ], 200);
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


    public function isWorkDayStarted()
    {
        $today = Carbon::now()->format('Y-m-d');
        $attendance = Attendance::where('date', $today)->exists();

        return response()->json(['work_day_started' => $attendance]);
    }
}
