<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPostingRequest extends FormRequest
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
            'job_title' => 'sometimes|required|string|max:255',
            'job_description' => 'sometimes|required|string',
            'employment_type' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'salary_range' => 'sometimes|nullable|string|max:255',
            'application_deadline' => 'sometimes|required|date',
            'isActive' => 'sometimes|boolean',
        ];
    }
}
