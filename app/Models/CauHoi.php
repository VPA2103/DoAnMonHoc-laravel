<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHoi extends Model
{
    use HasFactory;

    protected $table = 'cau_hoi';

    protected $fillable = ['noi_dung', 'ma_nguoi_dung'];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    public function traLois()
    {
        return $this->hasMany(TraLoi::class, 'ma_cau_hoi');
    }
}
