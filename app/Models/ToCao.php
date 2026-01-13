<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToCao extends Model
{
    use HasFactory;

    protected $table = 'to_cao';

    protected $fillable = ['noi_dung', 'ma_nguoi_dung'];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }
}

