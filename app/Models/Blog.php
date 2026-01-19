<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blog';
    protected $primaryKey = 'ma_blog';
    protected $fillable = [
        'tieu_de', 
        'slug',          
        'noi_dung', 
        'ma_nguoi_dung',
        'trang_thai'     
    ];
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }
}

