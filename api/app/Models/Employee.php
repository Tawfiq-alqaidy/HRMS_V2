<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'full_name',
        'birth_date',
        'gender',
        'phone',
        'picture',
        'basic_salary',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'resume_file',
        'isActive'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function adjustments()
    {
        return $this->hasMany(Adjustment::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function managedDepartment()
    {
        return $this->hasOne(Department::class, 'manager_employee_id');
    }
}
