<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietKeHoachBuaAn extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_ke_hoach_bua_an';

    protected $fillable = ['ma_ke_hoach', 'ma_cong_thuc'];

    public function keHoach()
    {
        return $this->belongsTo(KeHoachBuaAn::class, 'ma_ke_hoach');
    }

    public function congThuc()
    {
        return $this->belongsTo(CongThuc::class, 'ma_cong_thuc');
    }
}

