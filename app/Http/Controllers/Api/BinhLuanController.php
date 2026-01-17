<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;

class BinhLuanController extends Controller
{
    /**
     * Lấy danh sách bình luận của người đang đăng nhập
     */
    public function danhSachBinhLuanCuaToi()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $binhLuans = BinhLuan::with('congThuc')
            ->where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->where('trang_thai', 1)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $binhLuans
        ]);
    }

    /**
     * Xóa bình luận (chỉ chủ sở hữu mới được xóa)
     */
    public function xoaBinhLuan($id)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

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
