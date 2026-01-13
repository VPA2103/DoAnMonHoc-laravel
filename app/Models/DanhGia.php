<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'danh_gia';

    protected $fillable = ['so_sao', 'ma_nguoi_dung', 'ma_cong_thuc'];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    public function congThuc()
    {
        return $this->belongsTo(CongThuc::class, 'ma_cong_thuc');
    }
}

