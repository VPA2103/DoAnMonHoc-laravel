<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\NguoiDungController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordOtpController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::post('/upload', [UploadController::class, 'upload']);

Route::get('/test-cloudinary', function () {
    return config('cloudinary.cloud_url');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);



Route::middleware(['auth:api', 'vai_tro:user'])->group(function () {
    Route::get('/profile', [NguoiDungController::class, 'GetNguoiDungID']);
    Route::post('/profile/edit', [NguoiDungController::class, 'updateProfile']);
});

Route::middleware(['auth:api', 'vai_tro:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'GetNguoiDungID']);
    Route::get('/admin/danh-muc', [DanhMucController::class, 'index']);
    Route::post('/admin/danh-muc', [DanhMucController::class, 'store']);
    Route::put('/admin/danh-muc/{id}', [DanhMucController::class, 'update']);
    Route::delete('/admin/danh-muc/{id}', [DanhMucController::class, 'destroy']);
});

Route::post('/send-otp', [PasswordOtpController::class, 'sendOtp']);
Route::post('/reset-password-otp', [PasswordOtpController::class, 'resetPassword']);