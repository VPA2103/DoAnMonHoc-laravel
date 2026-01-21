<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CongThuc;
use App\Models\YeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CongThucController extends Controller
{
    /**
     * GET /api/user/cong-thuc
     * Danh sách công thức của USER
     */
    public function index()
    {
        $userId = auth()->id();

        $congThucs = CongThuc::with('danhMuc')
            ->where('ma_nguoi_dung', $userId)
            ->orderByDesc('ma_cong_thuc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $congThucs
        ]);
    }

    /**
     * POST /api/user/cong-thuc
     * Thêm công thức
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_cong_thuc'  => 'required|string|max:255',
            'ma_danh_muc'    => 'required|exists:danh_muc,ma_danh_muc',
            'do_kho'         => 'nullable|in:De,Trung binh,Kho',
            'mo_ta'          => 'nullable|string',
            'thoi_gian_nau'  => 'required|integer|min:1',
            'anh_cong_thuc'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['ma_nguoi_dung'] = auth()->id();
        $validated['slug'] = Str::slug($validated['ten_cong_thuc']);
        $validated['thoi_gian_nau'] = (int) $validated['thoi_gian_nau'];
        $validated['trang_thai'] = 1; // nháp

        if ($request->hasFile('anh_cong_thuc')) {
            $validated['anh_cong_thuc'] =
                $request->file('anh_cong_thuc')->store('cong-thuc', 'public');
        }

        $congThuc = CongThuc::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Thêm công thức thành công',
            'data' => $congThuc
        ], 201);
    }

    /**
     * GET /api/user/cong-thuc/{id}
     */
    public function show($id)
    {
        $congThuc = CongThuc::with(['danhMuc', 'nguyenLieus', 'buocNaus'])
            ->where('ma_cong_thuc', $id)
            ->where('ma_nguoi_dung', auth()->id())
            ->first();

        if (!$congThuc) {
            return response()->json([
                'message' => 'Không tìm thấy công thức'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $congThuc
        ]);
    }

    /**
     * PUT /api/user/cong-thuc/{id}
     */
    public function update(Request $request, $id)
    {
        $congThuc = CongThuc::where('ma_cong_thuc', $id)
            ->where('ma_nguoi_dung', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'ten_cong_thuc' => 'required|string|max:255',
            'ma_danh_muc'   => 'required|exists:danh_muc,ma_danh_muc',
            'mo_ta'         => 'nullable|string',
            'do_kho'        => 'nullable|in:De,Trung binh,Kho',
            'thoi_gian_nau' => 'required|integer|min:1',
            'anh_cong_thuc' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['ten_cong_thuc']);
        $validated['thoi_gian_nau'] = (int) $validated['thoi_gian_nau'];

        if ($request->hasFile('anh_cong_thuc')) {
            if ($congThuc->anh_cong_thuc) {
                Storage::disk('public')->delete($congThuc->anh_cong_thuc);
            }

            $validated['anh_cong_thuc'] =
                $request->file('anh_cong_thuc')->store('cong-thuc', 'public');
        }

        $congThuc->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật công thức thành công'
        ]);
    }

    /**
     * DELETE /api/user/cong-thuc/{id}
     */
    public function destroy($id)
    {
        $congThuc = CongThuc::where('ma_cong_thuc', $id)
            ->where('ma_nguoi_dung', auth()->id())
            ->firstOrFail();

        $congThuc->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa công thức thành công'
        ]);
    }
}
