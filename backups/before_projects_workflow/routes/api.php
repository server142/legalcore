<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DirectoryProfileController;

Route::prefix('v1')->group(function () {
    // Public directory endpoint
    Route::get('/verified-lawyers', [DirectoryProfileController::class, 'index']);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
