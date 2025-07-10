<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

// Direct image serving route for employee photos
Route::get('/employee-photos/{filename}', function ($filename) {
    $fullPath = 'employees/photos/' . $filename;

    if (Storage::disk('public')->exists($fullPath)) {
        $file = Storage::disk('public')->get($fullPath);

        // Determine MIME type from file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $mimeType = match (strtolower($extension)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream'
        };

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    abort(404);
})->where('filename', '[A-Za-z0-9\-_\.]+');

// Debug route to test storage access
Route::get('/test-storage', function () {
    $files = Storage::disk('public')->files('employees/photos');
    $urls = [];
    foreach ($files as $file) {
        $urls[] = [
            'file' => $file,
            'url' => url('employee-photos/' . basename($file)),
            'exists' => Storage::disk('public')->exists($file)
        ];
    }
    return response()->json($urls);
});
