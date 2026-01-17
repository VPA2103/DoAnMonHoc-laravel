<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinhLuan extends Model
{
    use HasFactory;

    protected $table = 'binh_luan';

    protected $primaryKey = 'ma_binh_luan';

    public $timestamps = true;

    protected $fillable = [
        'noi_dung',
        'ma_nguoi_dung',
        'ma_cong_thuc',
        'trang_thai'
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    public function congThuc()
    {
        return $this->belongsTo(CongThuc::class, 'ma_cong_thuc');
    }
}
