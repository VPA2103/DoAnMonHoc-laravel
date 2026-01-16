<?php

namespace App\Http\Controllers\Api;
use App\Models\NguoiDung;
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

    public function HienThiDSNguoiDung()
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

    public function TaoNguoiDung(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'ten_nguoi_dung' => 'required|string|min:3|max:255',
                'email'          => 'required|email|unique:nguoi_dung,email',
                'mat_khau'       => 'required|string|min:6',
                'vai_tro'        => 'required|in:admin,user',
                'anh_dai_dien'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'ten_nguoi_dung.required' => 'Tên người dùng không được để trống',
                'ten_nguoi_dung.min'      => 'Tên người dùng tối thiểu 3 ký tự',

                'email.required' => 'Email không được để trống',
                'email.email'    => 'Email không đúng định dạng',
                'email.unique'   => 'Email đã tồn tại',

                'mat_khau.required' => 'Mật khẩu không được để trống',
                'mat_khau.min'      => 'Mật khẩu tối thiểu 6 ký tự',

                'vai_tro.required' => 'Vai trò không được để trống',
                'vai_tro.in'       => 'Vai trò chỉ có thể là admin hoặc user',

                'anh_dai_dien.image' => 'Ảnh đại diện phải là file ảnh',
                'anh_dai_dien.mimes' => 'Ảnh chỉ chấp nhận jpg, jpeg, png, webp',
                'anh_dai_dien.max'   => 'Ảnh tối đa 2MB',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $avatarUrl = null;

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

            $avatarUrl = $result['secure_url'];
        }

        $user = NguoiDung::create([
            'ten_nguoi_dung' => $request->ten_nguoi_dung,
            'email'          => $request->email,
            'mat_khau'       => bcrypt($request->mat_khau),
            'vai_tro'        => $request->vai_tro,
            'trang_thai'     => 1,
            'anh_dai_dien'   => $avatarUrl, // có hoặc null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm người dùng thành công',
            'data' => [
                'ma_nguoi_dung'  => $user->ma_nguoi_dung,
                'ten_nguoi_dung' => $user->ten_nguoi_dung,
                'email'          => $user->email,
                'vai_tro'        => $user->vai_tro,
                'trang_thai'     => $user->trang_thai,
                'anh_dai_dien'   => $user->anh_dai_dien,
            ]
        ], 201);
    }


    public function LayThongTinTheoId($id)
    {
        $user = NguoiDung::select(
            'ma_nguoi_dung',
            'ten_nguoi_dung',
            'email',
            'vai_tro',
            'trang_thai',
            'ngay_tao'
        )->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }


    public function CapNhapNguoiDungTheoId(Request $request, $id)
    {
        $user = NguoiDung::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại'
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'ten_nguoi_dung' => 'nullable|string|min:3|max:255',
                'email'          => 'nullable|email|unique:nguoi_dung,email,' . $id . ',ma_nguoi_dung',
                'mat_khau'       => 'nullable|string|min:6',
                'vai_tro'        => 'nullable|in:admin,user',
                'trang_thai'     => 'nullable|in:0,1',
                'anh_dai_dien'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ],
            [
                'ten_nguoi_dung.min' => 'Tên người dùng tối thiểu 3 ký tự',

                'email.email'  => 'Email không đúng định dạng',
                'email.unique' => 'Email đã tồn tại',

                'mat_khau.min' => 'Mật khẩu tối thiểu 6 ký tự',

                'vai_tro.in' => 'Vai trò chỉ có thể là admin hoặc user',

                'trang_thai.in' => 'Trạng thái chỉ có thể là 0 hoặc 1',

                'anh_dai_dien.image' => 'Ảnh đại diện phải là file ảnh',
                'anh_dai_dien.mimes' => 'Ảnh chỉ chấp nhận jpg, jpeg, png, webp',
                'anh_dai_dien.max'   => 'Ảnh tối đa 2MB',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }
        if ($request->filled('ten_nguoi_dung')) {
            $user->ten_nguoi_dung = $request->ten_nguoi_dung;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('mat_khau')) {
            $user->mat_khau = bcrypt($request->mat_khau);
        }

        if ($request->filled('vai_tro')) {
            $user->vai_tro = $request->vai_tro;
        }

        if ($request->has('trang_thai')) {
            $user->trang_thai = $request->trang_thai;
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
                [
                    'folder' => 'avatar-user',
                ]
            );

            $user->anh_dai_dien = $result['secure_url'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật người dùng thành công',
            'data' => [
                'ma_nguoi_dung'  => $user->ma_nguoi_dung,
                'ten_nguoi_dung' => $user->ten_nguoi_dung,
                'email'          => $user->email,
                'vai_tro'        => $user->vai_tro,
                'trang_thai'     => $user->trang_thai,
                'anh_dai_dien'   => $user->anh_dai_dien,
            ]
        ]);
    }

    public function XoaNguoiDungTheoId($id)
    {
        $user = NguoiDung::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoá người dùng thành công'
        ]);
    }




}