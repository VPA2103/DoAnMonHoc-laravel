<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CongThuc;
use App\Models\NguoiDung;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function GetNguoiDungID(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'ma_nguoi_dung' => $user->ma_nguoi_dung,
                'ten_nguoi_dung' => $user->ten_nguoi_dung,
                'email' => $user->email,
                'anh_dai_dien' => $user->anh_dai_dien,
                'vai_tro' => $user->vai_tro,
                'trang_thai' => $user->trang_thai,
                'ngay_tao' => $user->ngay_tao,
            ]
        ]);
    }

    public function getUserList()
    {
        $users = NguoiDung::select(
            'ma_nguoi_dung',
            'ten_nguoi_dung',
            'email',
            'vai_tro',
            'anh_dai_dien',
            'trang_thai',
            'ngay_tao'
        )->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }


    public function layTatCaCongThuc()
    {
        $congThucs = CongThuc::with([
            'danhMuc:ma_danh_muc,ten_danh_muc'
        ])
            ->orderBy('ma_cong_thuc', 'desc')
            ->get();

        return response()->json([
            'data' => $congThucs
        ]);
    }

    public function updateTrangThai(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:1,2'
        ]);

        $congThuc = CongThuc::find($id);

        if (!$congThuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy công thức'
            ], 404);
        }

        $congThuc->trang_thai = $request->trang_thai;
        $congThuc->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $congThuc
        ]);
    }
}