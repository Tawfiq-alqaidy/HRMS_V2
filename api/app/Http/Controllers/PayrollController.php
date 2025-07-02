<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PayrollResource;
use \App\Models\Payroll;
use App\Http\Requests\UpdatePayrollRequest;

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




    public function store(Request $request)
    {
        //
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
        $payroll = \App\Models\Payroll::find($id);
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
        $payroll = \App\Models\Payroll::find($id);
        if (!$payroll) {
            return response()->json(['message' => 'Payroll record not found.'], 404);
        }
        $payroll->delete();
        return response()->json(['message' => 'Payroll record deleted successfully.']);
    }
}
