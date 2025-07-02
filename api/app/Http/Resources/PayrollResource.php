<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'employee_name' => optional($this->employee)->FullName,
            'department' => optional($this->employee && $this->employee->department)->DepartmentName ?? null,
            'bank_name' => optional($this->employee)->BankName ?? null,
            'bank_account' => optional($this->employee)->BankAccount ?? null,
            'iban' => optional($this->employee)->IBAN ?? null,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'generated_at' => $this->generated_at,
            'basic_salary' => $this->basic_salary,
            'deduction' => $this->deduction,
            'bonus' => $this->bonus,
            'net_salary' => $this->net_salary,
        ];
    }
}
