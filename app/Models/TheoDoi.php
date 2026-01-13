<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoDoi extends Model
{
    use HasFactory;

    protected $table = 'theo_doi';

    protected $fillable = ['ma_nguoi_theo_doi', 'ma_nguoi_duoc_theo_doi'];

    public function nguoiTheoDoi()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_theo_doi');
    }

    public function nguoiDuocTheoDoi()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_duoc_theo_doi');
    }
}

