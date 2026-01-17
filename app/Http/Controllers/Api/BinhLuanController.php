<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class BinhLuanController extends Controller
{
    /**
     * Lấy danh sách bình luận của người đang đăng nhập
     */
    public function danhSachBinhLuanCuaToi()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $binhLuans = BinhLuan::with([
                'congThuc:ma_cong_thuc,ten_cong_thuc,slug'
            ])
            ->where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->where('trang_thai', 1)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $binhLuans
        ]);
    }

    /**
     * Thêm bình luận mới
     */
    // public function themBinhLuan(Request $request)
    // {
    //     $user = JWTAuth::parseToken()->authenticate();

    //     $data = $request->validate([
    //         'ma_cong_thuc' => 'required|integer|exists:cong_thuc,ma_cong_thuc',
    //         'noi_dung'     => 'required|string|max:1000',
    //     ]);

    //     $binhLuan = BinhLuan::create([
    //         'ma_cong_thuc'  => $data['ma_cong_thuc'],
    //         'noi_dung'      => $data['noi_dung'],
    //         'ma_nguoi_dung' => $user->ma_nguoi_dung,
    //         'trang_thai'    => 1,
    //     ]);

    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Thêm bình luận thành công',
    //         'data'    => $binhLuan
    //     ], 201);
    // }

    /**
     * Xóa bình luận (chỉ chủ sở hữu mới được xóa)
     */
    public function xoaBinhLuan($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $binhLuan = BinhLuan::where('ma_binh_luan', $id)
            ->where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->first();

        if (!$binhLuan) {
            return response()->json([
                'status'  => false,
                'message' => 'Bình luận không tồn tại hoặc bạn không có quyền xóa'
            ], 403);
        }

        $binhLuan->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa bình luận thành công'
        ]);
    }
}
