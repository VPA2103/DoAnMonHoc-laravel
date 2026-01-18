<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguyenLieu;
use Illuminate\Http\Request;

class NguyenLieuController extends Controller
{
    /**
     * GET /api/nguyen-lieu
     * Lấy danh sách nguyên liệu theo công thức
     */
    public function index(Request $request)
    {
        $query = NguyenLieu::query();

        if ($request->filled('ma_cong_thuc')) {
            $query->where('ma_cong_thuc', $request->ma_cong_thuc);
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('ma_nguyen_lieu', 'desc')->get()
            // 👉 tự động có created_at, updated_at
        ]);
    }

    /**
     * GET /api/nguyen-lieu/{id}
     */
    public function show($id)
    {
        $nguyenLieu = NguyenLieu::where('ma_nguyen_lieu', $id)->first();

        if (!$nguyenLieu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nguyên liệu'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $nguyenLieu
        ]);
    }

    /**
     * POST /api/nguyen-lieu
     */
    public function store(Request $request)
    {
        $request->validate([
            'ma_cong_thuc'     => 'required|exists:cong_thuc,ma_cong_thuc',
            'ten_nguyen_lieu'  => 'required|string|max:255',
            'don_vi_tinh'      => 'nullable|string|max:50',
            'so_luong'         => 'nullable|numeric',
        ]);

        $nguyenLieu = NguyenLieu::create([
            'ma_cong_thuc'    => $request->ma_cong_thuc,
            'ten_nguyen_lieu' => $request->ten_nguyen_lieu,
            'don_vi_tinh'     => $request->don_vi_tinh,
            'so_luong'        => $request->so_luong,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm nguyên liệu thành công',
            'data' => $nguyenLieu // có created_at
        ], 201);
    }

    /**
     * PUT /api/nguyen-lieu/{id}
     */
    public function update(Request $request, $id)
    {
        $nguyenLieu = NguyenLieu::where('ma_nguyen_lieu', $id)->firstOrFail();

        $request->validate([
            'ten_nguyen_lieu' => 'required|string|max:255',
            'don_vi_tinh'     => 'nullable|string|max:50',
            'so_luong'        => 'nullable|numeric',
        ]);

        $nguyenLieu->update([
            'ten_nguyen_lieu' => $request->ten_nguyen_lieu,
            'don_vi_tinh'     => $request->don_vi_tinh,
            'so_luong'        => $request->so_luong,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật nguyên liệu thành công',
            'data' => $nguyenLieu // updated_at tự đổi
        ]);
    }

    /**
     * DELETE /api/nguyen-lieu/{id}
     */
    public function destroy($id)
    {
        $nguyenLieu = NguyenLieu::where('ma_nguyen_lieu', $id)->first();

        if (!$nguyenLieu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nguyên liệu'
            ], 404);
        }

        $nguyenLieu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa nguyên liệu thành công'
        ]);
    }
}
