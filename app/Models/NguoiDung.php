<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class NguoiDung extends Authenticatable
{
    use HasFactory;

    protected $table = 'nguoi_dung';
    protected $primaryKey = 'ma_nguoi_dung';

    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'ten_nguoi_dung',
        'email',
        'mat_khau',
        'anh_dai_dien',
        'vai_tro',
        'trang_thai',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    // Công thức
    public function congThucs()
    {
        return $this->hasMany(CongThuc::class, 'ma_nguoi_dung');
    }

    // Bình luận
    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'ma_nguoi_dung');
    }

    // Đánh giá
    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'ma_nguoi_dung');
    }

    // Yêu thích
    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'ma_nguoi_dung');
    }

    // Theo dõi (người đang theo dõi)
    public function dangTheoDoi()
    {
        return $this->hasMany(TheoDoi::class, 'ma_nguoi_theo_doi');
    }

    // Blog
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'ma_nguoi_dung');
    }

    // Câu hỏi
    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'ma_nguoi_dung');
    }

    // Kế hoạch bữa ăn
    public function keHoachBuaAns()
    {
        return $this->hasMany(KeHoachBuaAn::class, 'ma_nguoi_dung');
    }

    // Tố cáo
    public function toCaos()
    {
        return $this->hasMany(ToCao::class, 'ma_nguoi_dung');
    }
}