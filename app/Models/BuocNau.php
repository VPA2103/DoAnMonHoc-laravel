<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuocNau extends Model
{
    use HasFactory;

    protected $table = 'buoc_nau';

    protected $fillable = ['noi_dung', 'ma_cong_thuc'];

    public function congThuc()
    {
        return $this->belongsTo(CongThuc::class, 'ma_cong_thuc');
    }
}