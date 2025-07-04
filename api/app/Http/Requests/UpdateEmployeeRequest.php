<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            'user_id' => 'sometimes|exists:users,id',
            'department_id' => 'sometimes|exists:departments,id',
            'full_name' => 'sometimes|string|max:255',
            'birth_date' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'phone' => 'sometimes|nullable|string|max:20',
            'picture' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'basic_salary' => 'sometimes|numeric|min:0',
            'bank_name' => 'sometimes|nullable|string|max:255',
            'bank_branch' => 'sometimes|nullable|string|max:255',
            'bank_account_number' => 'sometimes|nullable|string|max:255',
            'resume_file' => 'sometimes|nullable|file|mimes:pdf,doc,docx|max:5120',
            'isActive' => 'sometimes|boolean',
            'email' => 'sometimes|string|email|max:255',
            'password' => 'sometimes|nullable|string|min:6',
            'role' => 'sometimes|string|in:admin,hr,employee',
        ];
    }
}
