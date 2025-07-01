<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = ['job_posting_id', 'full_name', 'email', 'phone', 'cv_file_path', 'status'];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }
}
