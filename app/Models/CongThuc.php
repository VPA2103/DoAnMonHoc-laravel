<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CongThuc extends Model
{
    use HasFactory;

    protected $table = 'cong_thuc';

    // 🔴 DB không dùng id
    protected $primaryKey = 'ma_cong_thuc';

    public $timestamps = true;

    protected $fillable = [
        'ten_cong_thuc',
        'mo_ta',
        'ma_danh_muc',
        'ma_nguoi_dung',
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'ma_danh_muc');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    public function nguyenLieus()
    {
        return $this->hasMany(NguyenLieu::class, 'ma_cong_thuc');
    }

    public function buocNaus()
    {
        return $this->hasMany(BuocNau::class, 'ma_cong_thuc');
    }

    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'ma_cong_thuc')
                    ->where('trang_thai', 1)
                    ->orderByDesc('created_at');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'ma_cong_thuc');
    }

    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'ma_cong_thuc');
    }
}
