<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CongThuc extends Model
{
    use HasFactory;

    protected $table = 'cong_thuc';

    // 🔑 Khóa chính
    protected $primaryKey = 'ma_cong_thuc';
    public $incrementing = true;
    protected $keyType = 'int';

    // ⚠️ DB không dùng created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'ten_cong_thuc',
        'mo_ta',
        'ma_danh_muc',
        'ma_nguoi_dung',
        'do_kho',
        'thoi_gian_nau',
        'slug',
        'trang_thai',
        'anh_cong_thuc',
    ];

    /* ================= RELATIONS ================= */

    // 1 công thức thuộc 1 danh mục
    public function danhMuc()
    {
        return $this->belongsTo(
            DanhMuc::class,
            'ma_danh_muc',
            'ma_danh_muc'
        );
    }

    // Dùng để check công thức có đang được sử dụng không
    public function chiTietKeHoach()
    {
        return $this->hasMany(
            ChiTietKeHoachBuaAn::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
    }

    // 1 công thức thuộc 1 người dùng
    public function nguoiDung()
    {
        return $this->belongsTo(
            NguoiDung::class,
            'ma_nguoi_dung',
            'ma_nguoi_dung'
        );
    }

    // 1 công thức có nhiều nguyên liệu
    public function nguyenLieus()
    {
        // return $this->hasMany(
        //     NguyenLieu::class,
        //     'ma_cong_thuc',
        //     'ma_cong_thuc'
        // );
         return $this->belongsToMany(
        NguyenLieu::class,
        'cong_thuc_nguyen_lieu', // tên bảng pivot
        'ma_cong_thuc',          // foreign key trên pivot trỏ tới this model (cong_thuc)
        'ma_nguyen_lieu'         // foreign key trên pivot trỏ tới NguyenLieu
    )->withPivot('so_luong');    // nếu pivot chứa so_luong
    }

    // Các bước nấu
    public function buocNaus()
    {
        return $this->hasMany(
            BuocNau::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
    }

    // Bình luận (chỉ lấy bình luận đang hiển thị)
    public function binhLuans()
    {
        return $this->hasMany(
            BinhLuan::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        )
        ->where('trang_thai', 1)
        ->orderByDesc('created_at');
    }

    public function danhGias()
    {
        return $this->hasMany(
            DanhGia::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
    }

    public function yeuThichs()
    {
        return $this->hasMany(
            YeuThich::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
    }
}
