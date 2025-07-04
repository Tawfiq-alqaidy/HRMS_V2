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
        return [
            'id' => $this->id,
            'department_name' => $this->department ? $this->department->name : "whithout department",
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->user ? $this->user->email : 'No email',
            'status' => $this->isActive ? 'active' : 'inactive',
        ];
    }
}
