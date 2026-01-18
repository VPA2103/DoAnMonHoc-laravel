<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeHoachBuaAn;
use App\Models\ChiTietKeHoachBuaAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeHoachBuaAnController extends Controller
{
    // 📌 LẤY DANH SÁCH
    // 📌 LẤY DANH SÁCH (PUBLIC)
    public function index()
    {
        $keHoachs = KeHoachBuaAn::with([
            'chiTiet.congThuc:ma_cong_thuc,ten_cong_thuc,anh_cong_thuc'
        ])
            ->orderBy('ngay', 'desc')
            ->get();

        return response()->json([
            'data' => $keHoachs
        ], 200);
    }


    // 📌 LẤY THEO ID
    // 📌 LẤY THEO ID (PUBLIC)
    public function show($id)
    {
        $keHoach = KeHoachBuaAn::with([
            'chiTiet.congThuc:ma_cong_thuc,ten_cong_thuc,anh_cong_thuc'
        ])
            ->where('ma_ke_hoach', $id)
            ->first();

        if (!$keHoach) {
            return response()->json([
                'message' => 'Không tìm thấy kế hoạch'
            ], 404);
        }

        return response()->json([
            'data' => $keHoach
        ], 200);
    }


    // ➕ THÊM KẾ HOẠCH
    public function store(Request $request)
    {
        $request->validate([
            'ngay' => 'required|date',
            'ghi_chu' => 'nullable|string',
            'chi_tiet' => 'required|array|min:1',
            'chi_tiet.*.bua_an' => 'required|in:Sang,Trua,Toi,Phu',
            'chi_tiet.*.ma_cong_thuc' => 'required|exists:cong_thuc,ma_cong_thuc',
        ]);

        DB::beginTransaction();

        try {
            $keHoach = KeHoachBuaAn::create([
                'ma_nguoi_dung' => auth('api')->id(),
                'ngay' => $request->ngay,
                'ghi_chu' => $request->ghi_chu,
            ]);

            foreach ($request->chi_tiet as $ct) {
                ChiTietKeHoachBuaAn::create([
                    'ma_ke_hoach' => $keHoach->ma_ke_hoach,
                    'ma_cong_thuc' => $ct['ma_cong_thuc'],
                    'bua_an' => $ct['bua_an'],
                    'ngay_an' => $request->ngay,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Thêm kế hoạch thành công',
                'data' => $keHoach->load('chiTiet')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Thêm kế hoạch thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // ✏️ SỬA KẾ HOẠCH
    public function update(Request $request, $id)
    {
        $keHoach = KeHoachBuaAn::where('ma_ke_hoach', $id)
            ->where('ma_nguoi_dung', auth('api')->id())
            ->first();

        if (!$keHoach) {
            return response()->json([
                'message' => 'Không tìm thấy kế hoạch'
            ], 404);
        }

        $request->validate([
            'ghi_chu' => 'nullable|string',
            'chi_tiet' => 'required|array|min:1',
            'chi_tiet.*.bua_an' => 'required|in:Sang,Trua,Toi,Phu',
            'chi_tiet.*.ma_cong_thuc' => 'required|exists:cong_thuc,ma_cong_thuc',
        ]);

        DB::beginTransaction();

        try {
            // cập nhật ghi chú
            $keHoach->update([
                'ghi_chu' => $request->ghi_chu,
            ]);

            // xóa chi tiết cũ
            $keHoach->chiTiet()->delete();

            // thêm chi tiết mới
            foreach ($request->chi_tiet as $ct) {
                ChiTietKeHoachBuaAn::create([
                    'ma_ke_hoach' => $keHoach->ma_ke_hoach,
                    'ma_cong_thuc' => $ct['ma_cong_thuc'],
                    'bua_an' => $ct['bua_an'],
                    'ngay_an' => $keHoach->ngay,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật kế hoạch thành công',
                'data' => $keHoach->load('chiTiet')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Cập nhật thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // 🗑️ XÓA
    public function destroy($id)
    {
        $keHoach = KeHoachBuaAn::where('ma_ke_hoach', $id)
            ->where('ma_nguoi_dung', auth('api')->id())
            ->first();

        if (!$keHoach) {
            return response()->json([
                'message' => 'Không tìm thấy kế hoạch'
            ], 404);
        }

        $keHoach->delete();

        return response()->json([
            'message' => 'Xóa kế hoạch thành công'
        ]);
    }
}
