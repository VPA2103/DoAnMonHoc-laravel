<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToCao extends Model
{
    use HasFactory;

    protected $table = 'to_cao';
    protected $primaryKey = 'ma_to_cao';
    protected $fillable = [
        'noi_dung',
        'danh_muc_to_cao',
        'ma_nguoi_dung',
        'trang_thai'
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }
}

