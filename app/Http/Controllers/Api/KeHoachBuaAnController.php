<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeHoachBuaAn;
use App\Models\ChiTietKeHoachBuaAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeHoachBuaAnController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'ma_nguoi_dung' => 'required|exists:nguoi_dung,ma_nguoi_dung',
                'ngay' => 'required|date',
                'ten_bua_an' => 'required|string|max:50',
                'ghi_chu' => 'nullable|string',

                'chi_tiet' => 'required|array|min:1',
                'chi_tiet.*.ma_cong_thuc' => 'required|exists:cong_thuc,ma_cong_thuc',
                'chi_tiet.*.ngay_an' => 'required|date',
                'chi_tiet.*.bua_an' => 'required|in:Sang,Trua,Toi,Phu',
            ],
            [
                'ma_nguoi_dung.required' => 'Người dùng không được để trống',
                'ma_nguoi_dung.exists' => 'Người dùng không tồn tại',

                'ngay.required' => 'Ngày kế hoạch không được để trống',
                'ngay.date' => 'Ngày không hợp lệ',

                'chi_tiet.required' => 'Phải có ít nhất 1 món ăn',
                'chi_tiet.*.ma_cong_thuc.exists' => 'Công thức không tồn tại',
                'chi_tiet.*.bua_an.in' => 'Bữa ăn không hợp lệ',
            ]
        );

        DB::beginTransaction();
        try {
            // 1️⃣ Tạo kế hoạch
            $keHoach = KeHoachBuaAn::create([
                'ma_nguoi_dung' => $validated['ma_nguoi_dung'],
                'ngay' => $validated['ngay'],
                'ten_bua_an' => $validated['ten_bua_an'],
                'ghi_chu' => $validated['ghi_chu'] ?? null,
            ]);

            // 2️⃣ Tạo chi tiết
            foreach ($validated['chi_tiet'] as $item) {
                ChiTietKeHoachBuaAn::create([
                    'ma_ke_hoach' => $keHoach->ma_ke_hoach,
                    'ma_cong_thuc' => $item['ma_cong_thuc'],
                    'ngay_an' => $item['ngay_an'],
                    'bua_an' => $item['bua_an'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Thêm kế hoạch bữa ăn thành công',
                'data' => $keHoach->load('chiTiet')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
