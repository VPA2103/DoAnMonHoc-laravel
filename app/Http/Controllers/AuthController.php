<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Auth\FakeUser;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage; 


class AuthController extends Controller
{
    private $fakeUsers = [
        [
            'email' => 'admin@gmail.com',
            'password' => '123456',
            'role' => 'admin',
            'name' => 'Admin'
        ],
        
    ];
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Tìm user theo email
        $user = NguoiDung::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email không tồn tại'
            ], 401);
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($request->password, $user->mat_khau)) {
            return response()->json([
                'message' => 'Mật khẩu không đúng'
            ], 401);
        }

        // Kiểm tra trạng thái
        if ($user->trang_thai != 1) {
            return response()->json([
                'message' => 'Tài khoản đã bị khóa'
            ], 403);
        }

        // Tạo token JWT
        $token = JWTAuth::fromUser($user);

        // Chuẩn hóa đường dẫn ảnh khi trả về login
        $avatarUrl = $user->anh_dai_dien;
        if ($avatarUrl && !str_starts_with($avatarUrl, 'http')) {
            $avatarUrl = asset('storage/' . $avatarUrl);
        }
        
        return response()->json([
            'token' => $token,
            'token_type'   => 'Bearer',
            'user' => [
                'ma_nguoi_dung'       => $user->ma_nguoi_dung,// doi 'id' thanh 'ma_nguoi_dung' 
                'ten_nguoi_dung'      => $user->ten_nguoi_dung,
                'email'    => $user->email,
                'vai_tro'  => $user->vai_tro,
                'anh_dai_dien'   => $user->anh_dai_dien,
            ]
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_nguoi_dung' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:nguoi_dung,email',
            'mat_khau' => 'required|min:6|confirmed',
        ], [
            'ten_nguoi_dung.required' => 'Họ tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'mat_khau.required' => 'Mật khẩu không được để trống',
            'mat_khau.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'mat_khau.confirmed' => 'Mật khẩu nhập lại không khớp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $nguoiDung = NguoiDung::create([
            'ten_nguoi_dung' => $request->ten_nguoi_dung,
            'email' => $request->email,
            'mat_khau' => Hash::make($request->mat_khau),
            'vai_tro' => 'user',
            'trang_thai' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công',
            'data' => [
                'ma_nguoi_dung' => $nguoiDung->ma_nguoi_dung,
                'ten_nguoi_dung' => $nguoiDung->ten_nguoi_dung,
                'email' => $nguoiDung->email,
                'vai_tro' => $nguoiDung->vai_tro,
            ]
        ], 201);
    }

  

public function updateProfile(Request $request)
{
    $user = auth()->user(); 

    if (!$user && $request->has('ma_nguoi_dung')) {
            $user = NguoiDung::find($request->ma_nguoi_dung);
    }

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    // 1. Validate cơ bản
    $rules = [
        'ten_nguoi_dung' => 'required|string|max:255',
        'anh_dai_dien'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
    ];

    // 2. Nếu người dùng gửi mật khẩu cũ => Tức là muốn đổi mật khẩu => Thêm rule validate
    if ($request->filled('mat_khau_cu')) {
        $rules['mat_khau_cu']  = 'required';
        $rules['mat_khau_moi'] = 'required|min:6|confirmed'; // Tự động check khớp với mat_khau_moi_confirmation
    }

    $validator = Validator::make($request->all(), $rules, [
        'mat_khau_moi.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
        'mat_khau_moi.confirmed' => 'Xác nhận mật khẩu mới không khớp',
        'anh_dai_dien.max' => 'Ảnh quá lớn (tối đa 5MB)',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
    }

    // 3. Xử lý đổi mật khẩu (nếu có)
    if ($request->filled('mat_khau_cu')) {
        // Kiểm tra mật khẩu cũ có khớp trong DB không
        if (!Hash::check($request->mat_khau_cu, $user->mat_khau)) {
            return response()->json(['success' => false, 'message' => 'Mật khẩu cũ không chính xác'], 400);
        }
        // Đổi mật khẩu
        $user->mat_khau = Hash::make($request->mat_khau_moi);
    }

    // 4. Cập nhật thông tin khác
    $user->ten_nguoi_dung = $request->ten_nguoi_dung;

    if ($request->hasFile('anh_dai_dien')) {
        if ($user->anh_dai_dien && Storage::disk('public')->exists($user->anh_dai_dien)) {
            Storage::disk('public')->delete($user->anh_dai_dien);
        }
        $path = $request->file('anh_dai_dien')->store('avatars', 'public');
        $user->anh_dai_dien = $path;
    }

    $user->save();

    // 5. Trả về kết quả
    $fullAvatarUrl = $user->anh_dai_dien;
    if ($fullAvatarUrl && !str_starts_with($fullAvatarUrl, 'http')) {
            $fullAvatarUrl = asset('storage/' . $fullAvatarUrl);
    }

    return response()->json([
        'success' => true,
        'message' => 'Cập nhật hồ sơ thành công',
        'data'    => [
            'ma_nguoi_dung'  => $user->ma_nguoi_dung,
            'ten_nguoi_dung' => $user->ten_nguoi_dung,
            'email'          => $user->email,
            'vai_tro'        => $user->vai_tro,
            'anh_dai_dien'   => $fullAvatarUrl 
        ]
    ]);
}
}