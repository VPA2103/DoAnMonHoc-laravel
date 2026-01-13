<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguyenLieu extends Model
{
    use HasFactory;

    protected $table = 'nguyen_lieu';

    protected $fillable = ['ten_nguyen_lieu', 'ma_cong_thuc'];

    public function congThuc()
    {
        return $this->belongsTo(CongThuc::class, 'ma_cong_thuc');
    }
}