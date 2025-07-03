<?php

namespace App\Http\Controllers;


use App\Models\Adjustment;
use App\Models\Payroll;
use App\Http\Requests\StoreAdjustmentRequest;
use App\Http\Requests\UpdateAdjustmentRequest;
use App\Http\Resources\AdjustmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends Controller
{

    public function index()
    {
        $adjustments = Adjustment::with('employee')->get();
        if ($adjustments->isEmpty()) {
            return response()->json(['message' => 'No adjustments found.'], 404);
        }
        return AdjustmentResource::collection($adjustments);
    }



    public function store(StoreAdjustmentRequest $request)
    {
        $validated = $request->validated();
        $adjustment = Adjustment::create($validated);
        $adjustment->load('employee');
        return response()->json([
            'message' => 'Adjustment created successfully.',
        ], 201);
    }


    public function show(string $id)
    {
        $adjustment = Adjustment::with('employee')->findOrFail($id);
        if (!$adjustment) {
            return response()->json(['message' => 'Adjustment not found.'], 404);
        }
        return new AdjustmentResource($adjustment);
    }




    public function update(UpdateAdjustmentRequest $request, string $id)
    {
        $adjustment = Adjustment::findOrFail($id);
        $month = $adjustment->created_at->format('m');
        $year = $adjustment->created_at->format('Y');
        $payrollExists = Payroll::where('employee_id', $adjustment->employee_id)
            ->whereMonth('generated_at', $month)
            ->whereYear('generated_at', $year)
            ->exists();
        if ($payrollExists) {
            return response()->json(['message' => 'You cannot modify this adjustment because the salary has already been disbursed for this month.'], 403);
        }
        $adjustment->update($request->validated());
        $adjustment->load('employee');
        return new AdjustmentResource($adjustment);
    }


    public function destroy(string $id)
    {
        $adjustment = Adjustment::findOrFail($id);
        $month = $adjustment->created_at->format('m');
        $year = $adjustment->created_at->format('Y');
        $payrollExists = Payroll::where('employee_id', $adjustment->employee_id)
            ->whereMonth('generated_at', $month)
            ->whereYear('generated_at', $year)
            ->exists();
        if ($payrollExists) {
            return response()->json(['message' => 'You cannot delete this adjustment because the salary has already been disbursed for this month.'], 403);
        }
        $adjustment->delete();
        return response()->json(['message' => 'Adjustment deleted successfully.']);
    }
}
