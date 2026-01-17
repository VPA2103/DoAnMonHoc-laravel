<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CongThuc;

class CongThucController extends Controller
{
    /**
     * GET /api/cong-thuc
     * Lấy danh sách công thức (dùng cho dropdown)
     */
    public function index()
    {
        $data = CongThuc::query()
            ->select(
                'ma_cong_thuc',
                'ten_cong_thuc'
            )
            ->where('trang_thai', 1)       //  chỉ lấy công thức đang hoạt động
            ->orderBy('ten_cong_thuc')    //  sắp xếp cho dropdown dễ nhìn
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }
}
