<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,EmployeeID',
            'generated_at' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
        ];
    }
}
