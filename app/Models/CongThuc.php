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

    // ⚠️ Nếu bảng cong_thuc CÓ created_at, updated_at → đổi thành true
    public $timestamps = false;

    protected $fillable = [
        'ten_cong_thuc',
        'mo_ta',
        'ma_danh_muc',
        'ma_nguoi_dung',
        'slug',
        'do_kho',
        'thoi_gian_nau',
        'trang_thai'
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

    // 1 công thức thuộc 1 người dùng
    public function nguoiDung()
    {
        return $this->belongsTo(
            NguoiDung::class,
            'ma_nguoi_dung',
            'ma_nguoi_dung'
        );
    }

    // 🔥 1 công thức có NHIỀU nguyên liệu
    public function nguyenLieus()
    {
        return $this->hasMany(
            NguyenLieu::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
    }

    public function buocNaus()
    {
        return $this->hasMany(
            BuocNau::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
    }

    public function binhLuans()
    {
        return $this->hasMany(
            BinhLuan::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
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
