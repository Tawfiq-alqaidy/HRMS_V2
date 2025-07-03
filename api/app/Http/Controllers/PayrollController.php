<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PayrollResource;
use \App\Models\Payroll;
use App\Http\Requests\UpdatePayrollRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class PayrollController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payrolls = Payroll::with(['employee.department'])->get();
        if ($payrolls->isEmpty()) {
            return response()->json(['message' => 'No payroll records found.'], 404);
        }
        return PayrollResource::collection($payrolls);
    }







    public function show(string $id)
    {
        $payroll = Payroll::with(['employee.department'])->find($id);
        if (!$payroll) {
            return response()->json(['message' => 'Payroll record not found.'], 404);
        }
        return new PayrollResource($payroll);
    }


    public function update(\App\Http\Requests\UpdatePayrollRequest $request, string $id)
    {
        $payroll = Payroll::find($id);
        if (!$payroll) {
            return response()->json(['message' => 'Payroll record not found.'], 404);
        }
        $payroll->update($request->validated());
        return response()->json(['message' => 'Payroll record updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payroll = Payroll::find($id);
        if (!$payroll) {
            return response()->json(['message' => 'Payroll record not found.'], 404);
        }
        $payroll->delete();
        return response()->json(['message' => 'Payroll record deleted successfully.']);
    }

    //v2
    public function generateMonthlyPayroll(Request $request)
    {
        $now = now();
        $month = $request->input('month', $now->format('m'));
        $year = $request->input('year', $now->format('Y'));
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $deductionRate = (float) (DB::table('system_settings')->value('deduction_per_absence_day') ?? 0);
        $employees = Employee::where('isActive', 1)->get();

        // Fetch all attendances for the month, group by employee_id
        $attendances = DB::table('attendance')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('employee_id');

        // Fetch all adjustments for the month, group by employee_id
        $adjustments = DB::table('adjustments')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy('employee_id');

        $payrolls = [];
        $alreadyCreated = [];
        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                // Check if payroll already exists for this employee for the month/year
                $exists = Payroll::where('employee_id', $employee->id)
                    ->whereMonth('generated_at', $month)
                    ->whereYear('generated_at', $year)
                    ->exists();
                if ($exists) {
                    $alreadyCreated[] = $employee->id;
                    continue;
                }

                $basicSalary = (float) $employee->basic_salary;
                $allDays = collect(range(1, (int)date('t', strtotime($startDate))))
                    ->map(fn($d) => date('Y-m-d', strtotime("$year-$month-" . str_pad($d, 2, '0', STR_PAD_LEFT))));

                $empAttendance = $attendances->get($employee->id, collect())->pluck('date')->unique();
                $absenceDays = $allDays->diff($empAttendance)->count();
                $absenceDeduction = $basicSalary * $absenceDays * $deductionRate;

                $empAdjustments = $adjustments->get($employee->id, collect());
                $totalBonus = $empAdjustments->where('type', 'bonus')->sum('amount');
                $totalDeduction = $empAdjustments->where('type', 'deduction')->sum('amount');

                $payrolls[] = Payroll::create([
                    'employee_id' => $employee->id,
                    'generated_at' => $now,
                    'basic_salary' => $basicSalary,
                    'deduction' => $absenceDeduction + $totalDeduction,
                    'bonus' => $totalBonus,
                    'net_salary' => $basicSalary - $absenceDeduction - $totalDeduction + $totalBonus,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

        if (empty($payrolls)) {
            return response()->json([
                'message' => 'Payrolls for this month were already created for all employees.'
            ], 200);
        }

        return PayrollResource::collection(collect($payrolls));
    }

    //v1
    // public function generateMonthlyPayroll(Request $request)
    // {
    //     $now = now();
    //     $month = $request->input('month', $now->format('m'));
    //     $year = $request->input('year', $now->format('Y'));
    //     $startDate = "$year-$month-01";
    //     $endDate = date('Y-m-t', strtotime($startDate));

    //     // Get deduction rate from system settings
    //     $deductionRate = (float) (DB::table('system_settings')->value('deduction_per_absence_day') ?? 0);

    //     // Get all active employees
    //     $employees = Employee::where('isActive', 1)->get();
    //     $payrolls = [];

    //     foreach ($employees as $employee) {
    //         $basicSalary = (float) $employee->basic_salary;

    //         // Get all days in the month
    //         $allDays = collect(range(1, (int)date('t', strtotime($startDate))))
    //             ->map(fn($d) => date('Y-m-d', strtotime("$year-$month-" . str_pad($d, 2, '0', STR_PAD_LEFT))));

    //         // Get attendance days for this employee in the month
    //         $attendanceDays = DB::table('attendance')
    //             ->where('id', $employee->id)
    //             ->whereBetween('date', [$startDate, $endDate])
    //             ->pluck('date')
    //             ->unique();

    //         $absenceDays = $allDays->diff($attendanceDays)->count();
    //         $absenceDeduction = $basicSalary * $absenceDays * $deductionRate;

    //         // Get adjustments for this employee in the month
    //         $adjustments = DB::table('adjustments')
    //             ->where('employee_id', $employee->id)
    //             ->whereBetween('created_at', [$startDate, $endDate])
    //             ->get();

    //         $totalBonus = $adjustments->where('type', 'bonus')->sum('amount');
    //         $totalDeduction = $adjustments->where('type', 'deduction')->sum('amount');

    //         $netSalary = $basicSalary - $absenceDeduction - $totalDeduction + $totalBonus;

    //         $payroll = Payroll::create([
    //             'employee_id' => $employee->id,
    //             'generated_at' => $now,
    //             'basic_salary' => $basicSalary,
    //             'deduction' => $absenceDeduction + $totalDeduction,
    //             'bonus' => $totalBonus,
    //             'net_salary' => $netSalary,
    //         ]);

    //         $payrolls[] = $payroll;
    //     }

    //     return PayrollResource::collection(collect($payrolls));
    // }
}
