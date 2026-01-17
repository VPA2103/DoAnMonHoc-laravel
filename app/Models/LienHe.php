<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\NguoiDung;

class LienHe extends Model
{
    use HasFactory;

    protected $table = 'lien_he';
    protected $primaryKey = 'ma_lien_he';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'ma_nguoi_dung',
        'ho_ten',
        'email',
        'tieu_de',
        'noi_dung',
        'trang_thai',
        'ngay_gui',
    ];

    // Liên hệ thuộc về người dùng (có thể null)
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }
}
