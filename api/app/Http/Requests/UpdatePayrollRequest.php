<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayrollRequest extends FormRequest
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
            'employee_id' => 'sometimes|exists:employees,EmployeeID',
            'generated_at' => 'sometimes|date',
            'basic_salary' => 'sometimes|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'net_salary' => 'sometimes|numeric|min:0',
        ];
    }
}
