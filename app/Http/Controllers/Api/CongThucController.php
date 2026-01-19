<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CongThuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CongThucController extends Controller
{
    /**
     * GET /api/cong-thuc
     * Lấy danh sách công thức (full – admin)
     */
    public function index()
    {
        $maNguoiDung = Auth::id(); // == auth()->user()->ma_nguoi_dung

        $congThucs = CongThuc::with([
            'danhMuc:ma_danh_muc,ten_danh_muc'
        ])
            ->where('ma_nguoi_dung', $maNguoiDung)
            ->orderByDesc('ma_cong_thuc')
            ->get();

        return response()->json([
            'data' => $congThucs
        ], 200);
    }

    /**
     * GET /api/cong-thuc/dropdown
     * Lấy danh sách công thức cho dropdown
     */
    public function dropdown()
    {
        $data = CongThuc::query()
            ->select('ma_cong_thuc', 'ten_cong_thuc')
            ->where('trang_thai', 1)
            ->orderBy('ten_cong_thuc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $maNguoiDung = auth()->id();

        $congThuc = CongThuc::with([
            'danhMuc',
            'nguyenLieus',
            'buocNaus'
        ])
            ->where('ma_cong_thuc', $id)
            ->where('ma_nguoi_dung', $maNguoiDung)
            ->first();

        if (!$congThuc) {
            return response()->json([
                'message' => 'Không tìm thấy công thức hoặc bạn không có quyền'
            ], 404);
        }

        return response()->json([
            'data' => $congThuc
        ], 200);
    }

    // ➕ THÊM CÔNG THỨC
    public function store(Request $request)
    {
        $request->validate([
            'ten_cong_thuc' => 'required|string|max:255',
            'ma_danh_muc' => 'required|exists:danh_muc,ma_danh_muc',
            'do_kho' => 'nullable|in:De,Trung binh,Kho',
            'anh_cong_thuc' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'ten_cong_thuc',
            'ma_danh_muc',
            'do_kho',
            'mo_ta',
            'thoi_gian_nau'
        ]);

        $data['ma_nguoi_dung'] = auth()->id();
        $data['slug'] = Str::slug($request->ten_cong_thuc);

        if ($request->hasFile('anh_cong_thuc')) {
            $path = $request->file('anh_cong_thuc')->store('cong-thuc', 'public');
            $data['anh_cong_thuc'] = $path;
        }

        CongThuc::create($data);

        return response()->json([
            'message' => 'Thêm công thức thành công'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $congThuc = CongThuc::findOrFail($id);

        $validated = $request->validate([
            'ma_danh_muc' => 'required|exists:danh_muc,ma_danh_muc',
            'ten_cong_thuc' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'do_kho' => 'nullable|in:De,Trung binh,Kho',
            'thoi_gian_nau' => 'nullable|integer|min:1',
            'anh_cong_thuc' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['ten_cong_thuc']);

        if ($request->hasFile('anh_cong_thuc')) {
            if ($congThuc->anh_cong_thuc) {
                Storage::disk('public')->delete($congThuc->anh_cong_thuc);
            }

            $path = $request->file('anh_cong_thuc')->store('cong-thuc', 'public');
            $validated['anh_cong_thuc'] = $path;
        }

        $congThuc->update($validated);

        return response()->json([
            'message' => 'Cập nhật công thức thành công',
            'data' => $congThuc
        ]);
    }

    // 🗑️ XÓA CÔNG THỨC
    public function destroy($id)
    {
        $congThuc = CongThuc::findOrFail($id);

        if ($congThuc->ma_nguoi_dung !== auth()->id()) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa công thức này'
            ], 403);
        }

        if ($congThuc->chiTietKeHoach()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa công thức đang được sử dụng'
            ], 400);
        }

        $congThuc->delete();

        return response()->json([
            'message' => 'Xóa công thức thành công'
        ]);
    }

    public function updateTrangThai(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|boolean'
        ]);

        $congThuc = CongThuc::findOrFail($id);
        $congThuc->update([
            'trang_thai' => $request->trang_thai
        ]);

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }
}
