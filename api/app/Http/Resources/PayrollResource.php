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
            'employee_name' => optional($this->employee)->full_name,
            'department' => optional(optional($this->employee)->department)->name ?? null,
            'bank_name' => optional($this->employee)->bank_name ?? null,
            'bank_account' => optional($this->employee)->bank_account_number ?? null,
            'generated_at' => $this->generated_at,
            'basic_salary' => $this->basic_salary,
            'deduction' => $this->deduction,
            'bonus' => $this->bonus,
            'net_salary' => $this->net_salary,
        ];
    }
}
