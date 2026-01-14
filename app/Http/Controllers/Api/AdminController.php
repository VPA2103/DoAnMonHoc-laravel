<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}