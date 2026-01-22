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

   public function danhSachCongThuc(Request $request)
    {
        $query = CongThuc::with(['danhMuc', 'nguoiDung'])
            ->where('trang_thai', 2);

        // 🔍 lọc độ khó
        if ($request->filled('do_kho')) {
            $query->where('do_kho', $request->do_kho);
        }

        // 🔍 lọc danh mục
        if ($request->filled('ma_danh_muc')) {
            $query->where('ma_danh_muc', $request->ma_danh_muc);
        }

        $congThucs = $query
            ->orderByDesc('ma_cong_thuc')
            ->paginate(9);

        $data = $congThucs->getCollection()->map(function ($ct) {
            return [
                'ma_cong_thuc'  => $ct->ma_cong_thuc,
                'ten_cong_thuc' => $ct->ten_cong_thuc,
                'anh_cong_thuc' => $ct->anh_cong_thuc,
                'do_kho'        => $ct->do_kho,
                'thoi_gian_nau' => $ct->thoi_gian_nau,
                'danh_muc' => [
                    'ma_danh_muc'  => $ct->danhMuc->ma_danh_muc ?? null,
                    'ten_danh_muc' => $ct->danhMuc->ten_danh_muc ?? null,
                ],
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $congThucs->currentPage(),
                'last_page'    => $congThucs->lastPage(),
                'total'        => $congThucs->total(),
            ]
        ]);
    }
    public function chiTietCongThuc($maCongThuc)
    {
        $user = auth('api')->user();

        $ct = CongThuc::with([
                'danhMuc',
                'nguoiDung',
                'nguyenLieus',
                'buocNaus'
            ])
            ->where('ma_cong_thuc', $maCongThuc)
            ->where('trang_thai', 2)
            ->first();

        if (!$ct) {
            return response()->json([
                'status'  => false,
                'message' => 'Công thức không tồn tại'
            ], 404);
        }

        $isFavorite = false;
        if ($user) {
            $isFavorite = YeuThich::where([
                'ma_nguoi_dung' => $user->ma_nguoi_dung,
                'ma_cong_thuc'  => $ct->ma_cong_thuc
            ])->exists();
        }

        return response()->json([
            'status' => true,
            'data' => [
                'ma_cong_thuc'  => $ct->ma_cong_thuc,
                'ten_cong_thuc' => $ct->ten_cong_thuc,
                'mo_ta'         => $ct->mo_ta,
                'anh_cong_thuc' => $ct->anh_cong_thuc,
                'do_kho'        => $ct->do_kho,
                'thoi_gian_nau' => $ct->thoi_gian_nau,
                'slug'          => $ct->slug,

                'danh_muc' => [
                    'ma_danh_muc'  => $ct->danhMuc->ma_danh_muc ?? null,
                    'ten_danh_muc' => $ct->danhMuc->ten_danh_muc ?? null,
                ],

                'tac_gia' => [
                    'ma_nguoi_dung' => $ct->nguoiDung->ma_nguoi_dung ?? null,
                    'ten_nguoi_dung'=> $ct->nguoiDung->ten_nguoi_dung ?? null,
                    'anh_dai_dien'  => $ct->nguoiDung->anh_dai_dien ?? null,
                ],

                'nguyen_lieu' => $ct->nguyenLieus->map(fn ($nl) => [
                    'ten_nguyen_lieu' => $nl->ten_nguyen_lieu,
                    'so_luong'        => $nl->so_luong,
                ]),

                'buoc_nau' => $ct->buocNaus->map(fn ($b) => [
                    'thu_tu'  => $b->thu_tu,
                    'noi_dung'=> $b->noi_dung,
                ]),

                'is_favorite' => $isFavorite
            ]
        ]);
    }

    // public function loc(Request $request)
    // {
    //     $query = CongThuc::with(['danhMuc', 'nguoiDung'])
    //         ->where('trang_thai', 2); // chỉ công thức public

    //     // 🔍 Lọc theo độ khó
    //     if ($request->filled('do_kho')) {
    //         $query->where('do_kho', $request->do_kho);
    //     }

    //     // 🔍 Lọc theo danh mục
    //     if ($request->filled('ma_danh_muc')) {
    //         $query->where('ma_danh_muc', $request->ma_danh_muc);
    //     }

    //     $congThucs = $query
    //         ->orderByDesc('ma_cong_thuc')
    //         ->paginate(9);

    //     return response()->json([
    //         'status' => true,
    //         'data'   => $congThucs->items(),
    //         'pagination' => [
    //             'current_page' => $congThucs->currentPage(),
    //             'last_page'    => $congThucs->lastPage(),
    //             'total'        => $congThucs->total(),
    //         ]
    //     ]);
    // }

}
