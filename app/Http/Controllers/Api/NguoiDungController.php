<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NguoiDungController extends Controller
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

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'ten_nguoi_dung' => 'nullable|string|max:255',
            'anh_dai_dien'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cập nhật tên
        if ($request->filled('ten_nguoi_dung')) {
            $user->ten_nguoi_dung = $request->ten_nguoi_dung;
        }

        // ✅ Nếu có file ảnh → upload cloudinary
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
                [
                    'folder' => 'avatar-user',
                ]
            );

            // Lưu URL ảnh
            $user->anh_dai_dien = $result['secure_url'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => [
                'ten_nguoi_dung' => $user->ten_nguoi_dung,
                'anh_dai_dien' => $user->anh_dai_dien,
            ]
        ]);
    }
    
}