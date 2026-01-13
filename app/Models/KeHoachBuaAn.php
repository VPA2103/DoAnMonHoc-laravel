<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeHoachBuaAn extends Model
{
    use HasFactory;

    protected $table = 'ke_hoach_bua_an';

    protected $fillable = ['ten_ke_hoach', 'ma_nguoi_dung'];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    public function chiTiets()
    {
        return $this->hasMany(ChiTietKeHoachBuaAn::class, 'ma_ke_hoach');
    }
}

