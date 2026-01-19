<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Log;
class NguoiDung extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'nguoi_dung';
    protected $primaryKey = 'ma_nguoi_dung';

    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'ten_nguoi_dung',
        'email',
        'mat_khau',
        'anh_dai_dien',
        'vai_tro',
        'trang_thai',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    // Công thức
    public function congThucs()
    {
        return $this->hasMany(CongThuc::class, 'ma_nguoi_dung');
    }

    // Bình luận
    public function binhLuans()
    {
        return $this->hasMany(BinhLuan::class, 'ma_nguoi_dung');
    }

    // Đánh giá
    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'ma_nguoi_dung');
    }

    // Yêu thích
    public function yeuThichs()
    {
        return $this->hasMany(YeuThich::class, 'ma_nguoi_dung');
    }

    // Theo dõi (người đang theo dõi)
    public function dangTheoDoi()
    {
        return $this->belongsToMany(
            NguoiDung::class,
            'theo_doi',
            'ma_nguoi_theo_doi',
            'ma_nguoi_duoc_theo_doi'
        );
    }

    // Những người theo dõi tôi
    public function nguoiTheoDoi()
    {
        return $this->belongsToMany(
            NguoiDung::class,
            'theo_doi',
            'ma_nguoi_duoc_theo_doi',
            'ma_nguoi_theo_doi'
        );
    }

    // Blog
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'ma_nguoi_dung');
    }

    // Câu hỏi
    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'ma_nguoi_dung');
    }

    // Kế hoạch bữa ăn
    public function keHoachBuaAns()
    {
        return $this->hasMany(KeHoachBuaAn::class, 'ma_nguoi_dung');
    }

    // Tố cáo
    public function toCaos()
    {
        return $this->hasMany(ToCao::class, 'ma_nguoi_dung');
    }

    public function getJWTIdentifier()
    {
        // ⚠️ PHẢI string
        return (string) $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'vai_tro' => $this->vai_tro
        ];
    }

     public function getAuthPassword()
    {
        return $this->mat_khau;
    }

   public function updateProfile(Request $request)
{
    \Log::info('CALL updateProfile', [
        'auth_user' => auth()->user() ? auth()->user()->ma_nguoi_dung : null,
        'headers' => $request->headers->all(),
        'all' => $request->all()
    ]);

    $user = auth()->user();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
    }

    $validator = \Validator::make($request->all(), [
        'ten_nguoi_dung' => 'required|string|max:255',
        'anh_dai_dien'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        \Log::info('VALIDATION FAIL', ['errors' => $validator->errors()->toArray()]);
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    $user->ten_nguoi_dung = $request->ten_nguoi_dung;

    if ($request->hasFile('anh_dai_dien')) {
        try {
            $path = $request->file('anh_dai_dien')->store('avatars', 'public');
            $user->anh_dai_dien = $path;
        } catch (\Exception $e) {
            \Log::error('UPLOAD ERROR', ['msg' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Upload lỗi'], 500);
        }
    }

    try {
        $user->save();
    } catch (\Exception $e) {
        \Log::error('SAVE ERROR', ['msg' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Lưu thất bại'], 500);
    }

    \Log::info('UPDATE OK', ['user' => $user->ma_nguoi_dung]);
    return response()->json(['success' => true, 'message' => 'Cập nhật thông tin thành công', 'data' => $user]);
}


}