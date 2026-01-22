<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MahasiswaController; // <--- Import Controller Baru

// Route Login
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Wajib Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'profile']);
    
    // Route Resource Mahasiswa (Otomatis bikin semua endpoint CRUD)
    Route::apiResource('mahasiswa', MahasiswaController::class);
});