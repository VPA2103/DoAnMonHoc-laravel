<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraLoi extends Model
{
    use HasFactory;

    protected $table = 'tra_loi';

    protected $fillable = ['noi_dung', 'ma_cau_hoi', 'ma_nguoi_dung'];

    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'ma_cau_hoi');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }
}

