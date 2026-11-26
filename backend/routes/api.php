<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin')->get('/admin/dashboard', fn () => response()->json([
        'success' => true, 'message' => 'Admin dashboard',
    ]));

    Route::middleware('role:admin,manager')->get('/management', fn () => response()->json([
        'success' => true, 'message' => 'Management area',
    ]));

    Route::middleware('role:staff')->get('/staff/dashboard', fn () => response()->json([
        'success' => true, 'message' => 'Staff dashboard',
    ]));
});
