<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ToCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToCaoController extends Controller
{
    /* ================= USER ================= */

    // Gửi tố cáo
    public function store(Request $request)
    {
        $request->validate([
            'noi_dung' => 'required|string',
            'danh_muc_to_cao' => 'required|string'
        ]);

        ToCao::create([
            'noi_dung' => $request->noi_dung,
            'danh_muc_to_cao' => $request->danh_muc_to_cao,
            'ma_nguoi_dung' => Auth::id(),
            'trang_thai' => 'ChoDuyet'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi tố cáo thành công'
        ], 201);
    }

    // Xem tố cáo của tôi
    public function myToCao()
    {
        $toCaos = ToCao::with('nguoiDung:ma_nguoi_dung,ten_nguoi_dung')
            ->where('ma_nguoi_dung', Auth::id())
            ->orderByDesc('ma_to_cao')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $toCaos
        ]);
    }

    /* ================= ADMIN ================= */

    // Admin xem tất cả tố cáo
    public function index()
    {
        $toCaos = ToCao::with('nguoiDung:ma_nguoi_dung,ten_nguoi_dung')
            ->orderByDesc('ma_to_cao')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $toCaos
        ]);
    }

    // Admin duyệt / xử lý
    public function duyet(Request $request, $ma_to_cao)
    {
        $request->validate([
            'trang_thai' => 'required|in:ChoDuyet,DaXuLy'
        ]);

        $toCao = ToCao::findOrFail($ma_to_cao);
        $toCao->update([
            'trang_thai' => $request->trang_thai
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }
}
