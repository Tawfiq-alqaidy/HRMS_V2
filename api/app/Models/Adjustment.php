<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adjustment extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'type', 'amount', 'reason'];
    protected $table = 'adjustments';
    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
