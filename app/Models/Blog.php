<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blog';
    protected $primaryKey = 'ma_blog';
    public $timestamps = true;

    protected $fillable = [
        'tieu_de',
        'slug',
        'noi_dung',
        'hinh_anh',
        'ma_nguoi_dung',
        'trang_thai'
    ];

    // 🔥 TỰ ĐỘNG APPEND FIELD NÀY
    protected $appends = ['hinh_anh_url'];

    // 🔥 ACCESSOR CHUẨN
    public function getHinhAnhUrlAttribute()
    {
        if (!$this->hinh_anh) {
            return null;
        }

        return asset('storage/' . $this->hinh_anh);
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }
}

