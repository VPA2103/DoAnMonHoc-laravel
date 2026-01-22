<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguyenLieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminNguyenLieuController extends Controller
{
    /**
     * Kiểm tra user đã đăng nhập và có vai trò admin hay không.
     * Trả về null nếu ok; trả Response JSON nếu fail.
     */
    private function checkAdmin()
    {
        // Dùng guard 'api' để tương thích với token API hiện tại.
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        if (!isset($user->vai_tro) || $user->vai_tro !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền admin'
            ], 403);
        }

        return null;
    }

    /**
     * GET /api/admin/nguyen-lieu
     */
    public function index()
    {
        if ($res = $this->checkAdmin()) {
            return $res;
        }

        try {
            $nguyenLieus = NguyenLieu::orderBy('ma_nguyen_lieu', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $nguyenLieus
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi lấy danh sách nguyên liệu', ['err' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server'
            ], 500);
        }
    }

    /**
     * POST /api/admin/nguyen-lieu
     */
    public function store(Request $request)
    {
        if ($res = $this->checkAdmin()) {
            return $res;
        }

        $validated = $request->validate([
            'ten_nguyen_lieu'  => 'required|string|max:255',
            'don_vi_tinh'      => 'nullable|string|max:50',
        ]);

        // Làm sạch dữ liệu nhỏ
        $validated['ten_nguyen_lieu'] = trim($validated['ten_nguyen_lieu']);
        $validated['don_vi_tinh'] = isset($validated['don_vi_tinh']) ? trim($validated['don_vi_tinh']) : null;

        try {
            $nguyenLieu = NguyenLieu::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Thêm nguyên liệu thành công',
                'data' => $nguyenLieu
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi thêm nguyên liệu', ['err' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm nguyên liệu'
            ], 500);
        }
    }

    /**
     * PUT /api/admin/nguyen-lieu/{id}
     */
    public function update(Request $request, $id)
    {
        if ($res = $this->checkAdmin()) {
            return $res;
        }

        $nguyenLieu = NguyenLieu::where('ma_nguyen_lieu', $id)->firstOrFail();

        $validated = $request->validate([
            'ten_nguyen_lieu' => 'required|string|max:255',
            'don_vi_tinh'     => 'nullable|string|max:50',
        ]);

        $validated['ten_nguyen_lieu'] = trim($validated['ten_nguyen_lieu']);
        $validated['don_vi_tinh'] = isset($validated['don_vi_tinh']) ? trim($validated['don_vi_tinh']) : null;

        try {
            $nguyenLieu->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nguyên liệu thành công',
                'data' => $nguyenLieu
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Lỗi cập nhật nguyên liệu', ['id' => $id, 'err' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật nguyên liệu'
            ], 500);
        }
    }

    /**
     * DELETE /api/admin/nguyen-lieu/{id}
     */
    public function destroy($id)
    {
        if ($res = $this->checkAdmin()) {
            return $res;
        }

        try {
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
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Lỗi xóa nguyên liệu', ['id' => $id, 'err' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa nguyên liệu'
            ], 500);
        }
    }
}
