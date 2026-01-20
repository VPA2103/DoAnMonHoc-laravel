<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YeuThich;
use Illuminate\Http\Request;

class YeuThichController extends Controller
{
    /**
     *  Toggle yêu thích (thêm / xóa)
     */
     public function toggle(Request $request)
{
    $user = auth('api')->user();

    $data = $request->validate([
        'ma_cong_thuc' => 'required|exists:cong_thuc,ma_cong_thuc',
    ]);

    $yeuThich = YeuThich::where('ma_nguoi_dung', $user->ma_nguoi_dung)
        ->where('ma_cong_thuc', $data['ma_cong_thuc'])
        ->first();

    if ($yeuThich) {
        YeuThich::where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->where('ma_cong_thuc', $data['ma_cong_thuc'])
            ->delete();

        return response()->json([
            'status' => true,
            'liked' => false,
            'message' => 'Đã bỏ yêu thích'
        ]);
    }

    YeuThich::create([
        'ma_nguoi_dung' => $user->ma_nguoi_dung,
        'ma_cong_thuc' => $data['ma_cong_thuc'],
    ]);

    return response()->json([
        'status' => true,
        'liked' => true,
        'message' => 'Đã thêm yêu thích'
    ]);
}

    /**
     * Kiểm tra công thức đã được yêu thích chưa
     */
    public function check($maCongThuc)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'liked' => false
            ]);
        }

        $exists = YeuThich::where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->where('ma_cong_thuc', $maCongThuc)
            ->exists();

        return response()->json([
            'liked' => $exists
        ]);
    }

    /**
     *  Danh sách công thức yêu thích của tôi
     */
    public function danhSachYeuThich()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $data = YeuThich::with('congThuc')
            ->where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->get()
            ->map(function ($item) {
                return $item->congThuc;
            });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
