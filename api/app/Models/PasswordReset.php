<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_resets';
    public $timestamps = true;
    protected $fillable = ['email', 'otp', 'expires_at'];
    public $incrementing = false;
    protected $primaryKey = null;
    protected $dates = ['expires_at'];
}
