<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeHoachBuaAn extends Model
{
    protected $table = 'ke_hoach_bua_an';
    protected $primaryKey = 'ma_ke_hoach';

    protected $fillable = [
        'ma_nguoi_dung',
        'ngay',
        'ghi_chu'
    ];

    public function chiTiet()
    {
        return $this->hasMany(
            ChiTietKeHoachBuaAn::class,
            'ma_ke_hoach',
            'ma_ke_hoach'
        );
    }
}
