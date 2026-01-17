<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\NguoiDungController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordOtpController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Api\NguyenLieuController;
use App\Http\Controllers\Api\CongThucController;

use Illuminate\Support\Facades\Route;

Route::post('/upload', [UploadController::class, 'upload']);

Route::get('/test-cloudinary', function () {
    return config('cloudinary.cloud_url');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/cong-thuc', [CongThucController::class, 'index']);
Route::get('/cong-thuc/{id}', [CongThucController::class, 'show']);
Route::post('/cong-thuc', [CongThucController::class, 'store']);
Route::put('/cong-thuc/{id}', [CongThucController::class, 'update']);
Route::delete('/cong-thuc/{id}', [CongThucController::class, 'destroy']);

//Route::resource('nguyen-lieu', ...): Một dòng này tương đương với việc bạn viết 5 dòng get, post, put, delete thủ công. Nó sẽ tự tạo ra các đường dẫn:
Route::middleware(['auth:api', 'vai_tro:admin'])->group(function () {

    // ===== NGUYÊN LIỆU =====
    Route::get('/nguyen-lieu', [NguyenLieuController::class, 'index']);
    Route::get('/nguyen-lieu/{id}', [NguyenLieuController::class, 'show']);
    Route::post('/nguyen-lieu', [NguyenLieuController::class, 'store']);
    Route::put('/nguyen-lieu/{id}', [NguyenLieuController::class, 'update']);
    Route::delete('/nguyen-lieu/{id}', [NguyenLieuController::class, 'destroy']);

});
Route::get('/nguyen-lieu', [NguyenLieuController::class, 'index']);


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

    Route::get('/users', [NguoiDungController::class, 'HienThiDSNguoiDung']);
    Route::get('/users/{id}', [NguoiDungController::class, 'LayThongTinTheoId']);
    Route::post('/users', [NguoiDungController::class, 'TaoNguoiDung']);
    Route::post('/users/{id}', [NguoiDungController::class, 'CapNhapNguoiDungTheoId']);
    Route::delete('/users/{id}', [NguoiDungController::class, 'XoaNguoiDungTheoId']);

});

Route::post('/send-otp', [PasswordOtpController::class, 'sendOtp']);
Route::post('/reset-password-otp', [PasswordOtpController::class, 'resetPassword']);