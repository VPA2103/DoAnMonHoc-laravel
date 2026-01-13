<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Auth\FakeUser;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    private $fakeUsers = [
        [
            'id' => 1,
            'email' => 'admin@gmail.com',
            'password' => '123456',
            'role' => 'admin',
            'name' => 'Admin'
        ],
        [
            'id' => 2,
            'email' => 'user@gmail.com',
            'password' => '123456',
            'role' => 'user',
            'name' => 'User'
        ]
    ];
    public function login(Request $request)
    {
        $user = collect($this->fakeUsers)->first(
            fn($u) =>
            $u['email'] === $request->email &&
                $u['password'] === $request->password
        );

        if (!$user) {
            return response()->json(['message' => 'Sai tài khoản'], 401);
        }

        $fakeUser = new FakeUser(
            (string) $user['id'],
            $user['email'],
            $user['role'],
            $user['name']
        );

        $token = JWTAuth::fromUser($fakeUser);

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
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

    
}