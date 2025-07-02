<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;
    protected $table = 'payrolls';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'generated_at',
        'basic_salary',
        'deduction',
        'bonus',
        'net_salary'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
