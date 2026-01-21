<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CongThuc;
use App\Models\YeuThich;
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

   public function danhSachCongThuc(Request $request)
    {
        $congThucs = CongThuc::with(['danhMuc', 'nguoiDung'])
            ->where('trang_thai', 2)
            ->orderByDesc('ma_cong_thuc')
            ->paginate(9);

        $data = $congThucs->getCollection()->map(function ($ct) {
            return [
                'ma_cong_thuc'  => $ct->ma_cong_thuc,
                'ten_cong_thuc' => $ct->ten_cong_thuc,
                'slug'          => $ct->slug,
                'anh_cong_thuc' => $ct->anh_cong_thuc,
                'do_kho'        => $ct->do_kho,
                'thoi_gian_nau' => $ct->thoi_gian_nau,

                'danh_muc' => [
                    'ma_danh_muc'  => optional($ct->danhMuc)->ma_danh_muc,
                    'ten_danh_muc' => optional($ct->danhMuc)->ten_danh_muc,
                ],

                'tac_gia' => [
                    'ma_nguoi_dung' => optional($ct->nguoiDung)->ma_nguoi_dung,
                    'ten_nguoi_dung'=> optional($ct->nguoiDung)->ten_nguoi_dung,
                    'anh_dai_dien'  => optional($ct->nguoiDung)->anh_dai_dien,
                ],
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $data,
            'pagination' => [
                'current_page' => $congThucs->currentPage(),
                'last_page'    => $congThucs->lastPage(),
                'per_page'     => $congThucs->perPage(),
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

}
