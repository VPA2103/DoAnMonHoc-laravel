<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietKeHoachBuaAn extends Model
{
    protected $table = 'chi_tiet_ke_hoach_bua_an';
    protected $primaryKey = 'ma_chi_tiet';

    protected $fillable = [
        'ma_ke_hoach',
        'ma_cong_thuc',
        'ngay_an',
        'bua_an'
    ];

    public function congThuc()
    {
        return $this->belongsTo(
            CongThuc::class,
            'ma_cong_thuc',
            'ma_cong_thuc'
        );
    }
}
