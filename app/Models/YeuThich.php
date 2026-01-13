<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuThich extends Model
{
    use HasFactory;

    protected $table = 'yeu_thich';

    protected $fillable = ['ma_nguoi_dung', 'ma_cong_thuc'];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    public function congThuc()
    {
        return $this->belongsTo(CongThuc::class, 'ma_cong_thuc');
    }
}

