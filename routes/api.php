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
use App\Http\Controllers\Api\DanhGiaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LienHeController;
use App\Http\Controllers\Api\BinhLuanController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\TheoDoiController;

use App\Http\Controllers\Api\YeuThichController;
Route::post('/upload', [UploadController::class, 'upload']);

Route::get('/test-cloudinary', function () {
    return config('cloudinary.cloud_url');
});

// xem đánh giá theo công thức 
Route::get('/danh-gia/cong-thuc/{id}', [DanhGiaController::class, 'theoCongThuc']);
Route::get('/danh-gia/thong-ke/{id}', [DanhGiaController::class, 'thongKeTheoCongThuc']); //thong ke danh gia theo cong thuc

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
Route::get('/ke-hoach', [KeHoachBuaAnController::class, 'LayDanhSachKeHoachTrangChu']);
Route::get('/ke-hoach/{id}', [KeHoachBuaAnController::class, 'LayKeHoachTrangChuId']);

//congthuc
Route::get('/cong-thuc', [CongThucController::class, 'index']);
Route::get('/cong-thuc/{id}', [CongThucController::class, 'show']);
Route::post('/cong-thuc', [CongThucController::class, 'store']);
Route::put('/cong-thuc/{id}', [CongThucController::class, 'update']);
Route::delete('/cong-thuc/{id}', [CongThucController::class, 'destroy']);

// ===== PUBLIC API (KHÔNG CẦN LOGIN) =====
Route::get('/cong-thucc', [CongThucController::class, 'danhSachCongThuc']);
Route::get('/cong-thucc/{id}', [CongThucController::class, 'chiTietCongThuc']); 

Route::prefix('blogs')->group(function () {
    // Public: ai cũng xem được danh sách và chi tiết blog
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/{id}', [BlogController::class, 'show']);

 
});

//binh luan
Route::get(
    '/binh-luan/cong-thuc/{id}',
    [BinhLuanController::class, 'danhSachTheoCongThuc']
);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/profile', [NguoiDungController::class, 'GetNguoiDungID']);
    Route::post('/profile/edit', [AuthController::class, 'updateProfile']);

    //binh luan
    Route::get('/user/binh-luan/toi', [BinhLuanController::class, 'danhSachBinhLuanCuaToi']);
    Route::post('/user/binh-luan', [BinhLuanController::class, 'themBinhLuan']);
    Route::put('/user/binh-luan/{id}', [BinhLuanController::class, 'suaBinhLuan']);
    Route::delete('/user/binh-luan/{id}', [BinhLuanController::class, 'xoaBinhLuan']);


    //cong thuc
    Route::get('/user/cong-thuc', [CongThucController::class, 'index']);
    Route::put('/user/cong-thuc/{id}', [CongThucController::class, 'update']);
    Route::delete('/user/cong-thuc/{id}', [CongThucController::class, 'destroy']);
    Route::post('/user/cong-thuc', [CongThucController::class, 'store']);
    Route::get('/user/cong-thuc/{id}', [CongThucController::class, 'show']);

    //theo doi
    Route::get('/user/following', [NguoiDungController::class, 'DanhSachNguoiDangTheoDoi']);
    Route::post('/follow/{id}', [TheoDoiController::class, 'follow']); 
    Route::delete('/unfollow/{id}', [TheoDoiController::class, 'unfollow']);
    Route::get('/feed/blogs', [FeedController::class, 'blogs']);

    
    Route::get('/user/danh-muc', [DanhMucController::class, 'index']);


    //kehoach
    Route::get('/user/ke-hoach', [KeHoachBuaAnController::class, 'index']);
    Route::get('/user/ke-hoach/{id}', [KeHoachBuaAnController::class, 'show']);
    Route::post('/user/ke-hoach', [KeHoachBuaAnController::class, 'store']);
    Route::put('/user/ke-hoach/{id}', [KeHoachBuaAnController::class, 'update']);
    Route::delete('/user/ke-hoach/{id}', [KeHoachBuaAnController::class, 'destroy']);

    //danh gia
    Route::post('/danh-gia', [DanhGiaController::class, 'store']);
    Route::get('/user/danh-gia',[DanhGiaController::class, 'danhSachDanhGiaCuaToi']); // user quản lý đánh giá của mình
    Route::put('/danh-gia/{id}', [DanhGiaController::class, 'update']); //cap nhap danh gia
    Route::delete('/danh-gia/{id}', [DanhGiaController::class, 'destroy']); // xoa danh gia

    //yeu thich
    Route::post('/yeu-thich/toggle', [YeuThichController::class, 'toggle']);
    Route::get('/yeu-thich/check/{id}', [YeuThichController::class, 'check']);
    Route::get('/user/yeu-thich', [YeuThichController::class, 'danhSachYeuThich']);



    Route::post('blogs', [BlogController::class, 'store']);
    Route::put('blogs/{id}', [BlogController::class, 'update']);
    Route::delete('blogs/{id}', [BlogController::class, 'destroy']);
});

Route::middleware(['auth:api', 'vai_tro:admin'])->group(function () {
    Route::get('/admin/profile', [AdminController::class, 'GetNguoiDungID']);
    Route::post('/admin/profile', [AdminController::class, 'updateAdminProfile']);
    Route::get('/admin/users', [AdminController::class, 'getUserList']);


    Route::get('/admin/danh-muc', [DanhMucController::class, 'index']);
    Route::post('/admin/danh-muc', [DanhMucController::class, 'store']);
    Route::put('/admin/danh-muc/{id}', [DanhMucController::class, 'update']);
    Route::delete('/admin/danh-muc/{id}', [DanhMucController::class, 'destroy']);

    Route::get('/admin/cong-thuc', [AdminController::class, 'layTatCaCongThuc']);
    Route::put('/admin/cong-thuc/{id}', [AdminController::class, 'updateTrangThai']);


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

    //danh gia 
    Route::get('/admin/danh-gia', [DanhGiaController::class, 'index']);

});


