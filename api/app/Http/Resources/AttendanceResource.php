<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'date' => $this->date,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
        ];
    }
}
