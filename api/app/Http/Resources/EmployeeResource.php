<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isDetailedView = $request->route() && $request->route()->getActionMethod() === 'show';

        $baseData = [
            'id' => $this->id,
            'department' => $this->department ? [
                'id' => $this->department->id,
                'name' => $this->department->name
            ] : null,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->user ? $this->user->email : 'No email',
            'status' => $this->isActive ? 'active' : 'inactive',
        ];

        if ($isDetailedView) {
            return array_merge($baseData, [
                'birth_date' => $this->birth_date,
                'gender' => $this->gender,
                'picture' => $this->picture,
                'basic_salary' => $this->basic_salary,
                'bank_name' => $this->bank_name,
                'bank_branch' => $this->bank_branch,
                'bank_account_number' => $this->bank_account_number,
                'resume_file' => $this->resume_file,
            ]);
        }

        return $baseData;
    }
}
