<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;

Route::prefix('departments')->group(function () {
    Route::get('/ping', [DepartmentController::class, 'ping'])->name('departments.ping');
});
