<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CongThuc;
use App\Models\NguoiDung;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    public function updateAdminProfile(Request $request)
    {
        $admin = $request->user();

        $validator = Validator::make($request->all(), [
            'ten_nguoi_dung' => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255|unique:nguoi_dung,email,'
                . $admin->ma_nguoi_dung . ',ma_nguoi_dung',
            'anh_dai_dien'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->filled('ten_nguoi_dung')) {
            $admin->ten_nguoi_dung = $request->ten_nguoi_dung;
        }

        if ($request->filled('email')) {
            $admin->email = $request->email;
        }

        if ($request->hasFile('anh_dai_dien')) {
            

            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_KEY'),
                    'api_secret' => env('CLOUDINARY_SECRET'),
                ],
            ]);

            $result = $cloudinary->uploadApi()->upload(
                $request->file('anh_dai_dien')->getRealPath(),
                ['folder' => 'avatar-admin']
            );

            $admin->anh_dai_dien = $result['secure_url'];
        }

        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin admin thành công',
            'data' => [
                'ma_nguoi_dung'  => $admin->ma_nguoi_dung,
                'ten_nguoi_dung' => $admin->ten_nguoi_dung,
                'email'          => $admin->email,
                'anh_dai_dien'   => $admin->anh_dai_dien,
                'vai_tro'        => $admin->vai_tro,
                'trang_thai'     => $admin->trang_thai,
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