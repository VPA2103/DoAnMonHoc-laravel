<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CongThuc;
class NguyenLieu extends Model
{
    protected $table = 'nguyen_lieu';

    protected $primaryKey = 'ma_nguyen_lieu';

    public $incrementing = true;
    protected $keyType = 'int';

    // ✅ BẬT timestamps để tự ghi created_at / updated_at
    public $timestamps = false;

    // ✅ Cho phép create / update
    protected $fillable = [
        'ma_cong_thuc',
        'ten_nguyen_lieu',
        'don_vi_tinh',
        'so_luong'
    ];

    /**
     * 🔗 Quan hệ: Nguyên liệu thuộc về 1 công thức
     */
    public function congThuc()
    {
        return $this->belongsTo(
            CongThuc::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
    }
}
