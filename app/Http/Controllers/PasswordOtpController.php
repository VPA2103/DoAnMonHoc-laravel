<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordOtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nguoi_dung,email'
        ]);

        $otp = rand(100000, 999999);

        // Xóa OTP cũ
        DB::table('password_otps')->where('email', $request->email)->delete();

        // Lưu OTP mới
        DB::table('password_otps')->insert([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gửi mail
        Mail::raw("Mã OTP đổi mật khẩu của bạn là: $otp (có hiệu lực 5 phút)", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('OTP Đổi mật khẩu');
        });

        return response()->json([
            'message' => 'Đã gửi OTP về email',
            'otp_debug' => $otp,

        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $otpRecord = DB::table('password_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'OTP không hợp lệ hoặc đã hết hạn'
            ], 400);
        }

        // Đổi mật khẩu
        DB::table('nguoi_dung')
            ->where('email', $request->email)
            ->update([
                'mat_khau' => Hash::make($request->password)
            ]);

        // Xóa OTP sau khi dùng
        DB::table('password_otps')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Đổi mật khẩu thành công',
        ]);
    }
}