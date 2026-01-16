<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc';
    protected $primaryKey = 'ma_danh_muc'; // 👈 thêm
    public $timestamps = true; // nếu dùng cách A

    protected $fillable = ['ten_danh_muc', 'loai'];

    public function congThucs()
    {
        return $this->hasMany(CongThuc::class, 'ma_danh_muc');
    }
}

