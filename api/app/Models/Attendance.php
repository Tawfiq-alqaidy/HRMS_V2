<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendance';
    protected $fillable = ['employee_id', 'date', 'check_in_time', 'check_out_time'];
    public $timestamps = false;
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
