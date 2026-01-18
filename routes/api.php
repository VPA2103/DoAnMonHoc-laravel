<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CongThucController;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\KeHoachBuaAnController;
use App\Http\Controllers\Api\NguoiDungController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordOtpController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Api\NguyenLieuController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LienHeController;
use App\Http\Controllers\Api\BinhLuanController;

Route::post('/upload', [UploadController::class, 'upload']);

Route::get('/test-cloudinary', function () {
    return config('cloudinary.cloud_url');
});
// danh sách liên hệ
Route::post('/lien-he', [LienHeController::class, 'guiLienHe']);
Route::get('/lien-he', [LienHeController::class, 'danhSachLienHe']);
Route::delete('/lien-he/{id}', [LienHeController::class, 'xoaLienHe']);

//gui otp
Route::post('/send-otp', [PasswordOtpController::class, 'sendOtp']);
Route::post('/reset-password-otp', [PasswordOtpController::class, 'resetPassword']);

//login register

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// ke hoach
Route::get('/ke-hoach', [KeHoachBuaAnController::class, 'index']);
Route::get('/ke-hoach/{id}', [KeHoachBuaAnController::class, 'show']);

//congthuc

Route::get('/cong-thuc', [CongThucController::class, 'index']);
Route::get('/cong-thuc/{id}', [CongThucController::class, 'show']);
Route::post('/cong-thuc', [CongThucController::class, 'store']);
Route::put('/cong-thuc/{id}', [CongThucController::class, 'update']);
Route::delete('/cong-thuc/{id}', [CongThucController::class, 'destroy']);





Route::middleware(['auth:api', 'vai_tro:user'])->group(function () {
    Route::get('/profile', [NguoiDungController::class, 'GetNguoiDungID']);
    Route::post('/profile/edit', [NguoiDungController::class, 'updateProfile']);

    
    Route::get('/binh-luan/cua-toi', [BinhLuanController::class, 'danhSachBinhLuanCuaToi']);
    Route::delete('/binh-luan/{id}', [BinhLuanController::class, 'xoaBinhLuan']);


    Route::get('/user/cong-thuc', [CongThucController::class, 'index']);
    Route::put('/user/cong-thuc/{id}', [CongThucController::class, 'update']);
    Route::delete('/user/cong-thuc/{id}', [CongThucController::class, 'destroy']);
    Route::post('/user/cong-thuc', [CongThucController::class, 'store']);
    Route::get('/user/cong-thuc/{id}', [CongThucController::class, 'show']);


    Route::get('/user/danh-muc', [DanhMucController::class, 'index']);


    //kehoach
    Route::post('/user/ke-hoach', [KeHoachBuaAnController::class, 'store']);
    Route::put('/user/ke-hoach/{id}', [KeHoachBuaAnController::class, 'update']);
    Route::delete('/user/ke-hoach/{id}', [KeHoachBuaAnController::class, 'destroy']);


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

    Route::get('/nguyen-lieu', [NguyenLieuController::class, 'index']);
    Route::get('/nguyen-lieu/{id}', [NguyenLieuController::class, 'show']);
    Route::post('/nguyen-lieu', [NguyenLieuController::class, 'store']);
    Route::put('/nguyen-lieu/{id}', [NguyenLieuController::class, 'update']);
    Route::delete('/nguyen-lieu/{id}', [NguyenLieuController::class, 'destroy']);

});

