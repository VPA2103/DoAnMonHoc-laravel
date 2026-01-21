<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CongThucController;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\KeHoachBuaAnController;
use App\Http\Controllers\Api\NguoiDungController;
use App\Http\Controllers\Api\SearchController;
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
use App\Http\Controllers\Api\CauHoiController;
use App\Http\Controllers\Api\BuocNauController;
use App\Http\Controllers\Api\YeuThichController;
use App\Http\Controllers\Api\ToCaoController;
use App\Http\Controllers\Api\AdminNguyenLieuController;
use App\Http\Controllers\Api\CongThucNguyenLieuController;

Route::post('/upload', [UploadController::class, 'upload']);

Route::get('/test-cloudinary', function () {
    return config('cloudinary.cloud_url');
});

//search
Route::get('/search', [SearchController::class, 'search']);


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


// hien danh sach danh mục len trang chu
Route::get('/danh-muc', [DanhMucController::class, 'index']);

//binh luan
Route::get(
    '/binh-luan/cong-thuc/{id}',
    [BinhLuanController::class, 'danhSachTheoCongThuc']
);

Route::middleware(['auth:api'])->group(function () {
    //to cao
    Route::post('/to-cao', [ToCaoController::class, 'store']);
    Route::get('/to-cao/me', [ToCaoController::class, 'myToCao']);



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
    // User thêm bước nấu mới
    Route::post('/user/buoc-nau', [BuocNauController::class, 'store']); 
    Route::put('/user/buoc-nau/{id}', [BuocNauController::class, 'update']); 
    Route::delete('/user/buoc-nau/{id}', [BuocNauController::class, 'destroy']);
    Route::get('/user/buoc-nau/cong-thuc/{id}', [BuocNauController::class, 'getStepsByRecipeId']);
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


    //blog
    Route::get('/user/blogs', [BlogController::class, 'blogCuaToi']);
    Route::post('blogs', [BlogController::class, 'store']);
    Route::put('blogs/{id}', [BlogController::class, 'update']);
    Route::delete('blogs/{id}', [BlogController::class, 'destroy']);


    //cau hoi cua user 
    Route::post('/cau-hoi', [CauHoiController::class, 'store']);
    Route::get('/cau-hoi-cua-toi', [CauHoiController::class, 'myQuestions']);
    Route::put('/blogs/{id}/trang-thai', [BlogController::class, 'updateTrangThai']);

    Route::get('/user/nguyen-lieu', [NguyenLieuController::class, 'index']);
    Route::post('/user/cong-thuc-nguyen-lieu',[CongThucNguyenLieuController::class, 'store']);
    Route::get('/user/cong-thuc/{id}/nguyen-lieu',[CongThucNguyenLieuController::class, 'indexByCongThuc']);
        Route::put('/user/cong-thuc-nguyen-lieu', [CongThucNguyenLieuController::class, 'update']);
    Route::delete('/user/cong-thuc-nguyen-lieu', [CongThucNguyenLieuController::class, 'destroy']);


});

Route::middleware(['auth:api', 'vai_tro:admin'])->group(function () {
    //to cao
    Route::get('/to-cao', [ToCaoController::class, 'index']);
    Route::put('/to-cao/{ma_to_cao}', [ToCaoController::class, 'duyet']);
    Route::delete('/admin/danh-muc-to-cao/{id}', [ToCaoController::class, 'danhMucDestroy']);


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


    //hien danh sach cau hoi
    Route::get('/admin/cau-hoi', [CauHoiController::class, 'adminIndex']);
    Route::post('/admin/cau-hoi/{maCauHoi}/tra-loi', [CauHoiController::class,'adminTraLoi']);


    //blog admin
    Route::get('/admin/blogs', [BlogController::class, 'indexAdmin']);
    Route::put('/admin/blogs/{id}/duyet', [BlogController::class, 'duyetBlog']);
    Route::get('/admin/blog-cho-duyet', [BlogController::class, 'blogChoDuyet']);
});


