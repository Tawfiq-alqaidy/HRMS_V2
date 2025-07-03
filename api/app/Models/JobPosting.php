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

    // protected $table = 'job_postings';
    public $timestamps = false;

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
