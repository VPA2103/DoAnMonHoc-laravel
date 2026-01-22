<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuocNau extends Model
{
    use HasFactory;

    protected $table = 'buoc_nau';
    protected $primaryKey = 'ma_buoc_nau';
    protected $fillable = ['ma_cong_thuc', 'so_thu_tu', 'noi_dung', 'hinh_anh','thoi_gian',];
    public $timestamps = false;
    public function congThuc()
    {
        return $this->belongsTo(CongThuc::class, 'ma_cong_thuc', 'ma_cong_thuc');
    }
}