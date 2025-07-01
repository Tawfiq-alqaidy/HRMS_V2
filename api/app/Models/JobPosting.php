<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_title',
        'job_description',
        'employment_type',
        'location',
        'salary_range',
        'application_deadline',
        'isActive'
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
