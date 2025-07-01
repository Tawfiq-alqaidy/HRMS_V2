<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Determine the route or action name
        $routeName = $request->route() ? $request->route()->getName() : null;

        // For index and show: show manager and employees count only
        if (in_array($routeName, ['departments.index', 'departments.show'])) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'manager_employee_id' => $this->manager_employee_id,
                'manager' => $this->manager ? [
                    'id' => $this->manager->id,
                    'name' => $this->manager->name,
                ] : null,
                'employees_count' => $this->employees_count ?? $this->employees()->count(),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }

        // For department employees: show all employees data
        if ($routeName === 'departments.employees') {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            ];
        }

        // Default: full resource
        return parent::toArray($request);
    }
}
