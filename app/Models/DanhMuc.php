<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc';

    protected $fillable = ['ten_danh_muc'];

    public function congThucs()
    {
        return $this->hasMany(CongThuc::class, 'ma_danh_muc');
    }
}

