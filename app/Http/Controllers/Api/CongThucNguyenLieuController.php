<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CongThuc;

class CongThucNguyenLieuController extends Controller
{
    /**
     * USER – gán nguyên liệu vào công thức
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'ma_cong_thuc'   => 'required|exists:cong_thuc,ma_cong_thuc',
            'ma_nguyen_lieu' => 'required|exists:nguyen_lieu,ma_nguyen_lieu',
            'so_luong'       => 'required|numeric|min:0.01',
        ]);

        $congThuc = CongThuc::findOrFail($data['ma_cong_thuc']);

        // ❗ Không cho trùng nguyên liệu
        if ($congThuc->nguyenLieus()->where('nguyen_lieu.ma_nguyen_lieu', $data['ma_nguyen_lieu'])->exists()) {
            return response()->json([
                'message' => 'Nguyên liệu đã tồn tại trong công thức'
            ], 422);
        }

        // ✅ Gán nguyên liệu bằng pivot
        $congThuc->nguyenLieus()->attach(
            $data['ma_nguyen_lieu'],
            ['so_luong' => $data['so_luong']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Thêm nguyên liệu vào công thức thành công'
        ], 201);
    }
    // GET /api/user/cong-thuc/{id}/nguyen-lieu
public function indexByCongThuc($id)
{
    $congThuc = \App\Models\CongThuc::with([
        'nguyenLieus' => function ($q) {
            $q->select(
                'nguyen_lieu.ma_nguyen_lieu',
                'ten_nguyen_lieu',
                'don_vi_tinh'
            );
        }
    ])->findOrFail($id);

    return response()->json([
        'data' => $congThuc->nguyenLieus
    ]);
}
   
   public function update(Request $request)
        {
            $data = $request->validate([
                'ma_cong_thuc'   => 'required|exists:cong_thuc,ma_cong_thuc',
                'ma_nguyen_lieu' => 'required|exists:nguyen_lieu,ma_nguyen_lieu',
                'so_luong'       => 'required|numeric|min:0'
            ]);

            $congThuc = CongThuc::findOrFail($data['ma_cong_thuc']);

            $congThuc->nguyenLieus()
                ->updateExistingPivot(
                    $data['ma_nguyen_lieu'],
                    ['so_luong' => $data['so_luong']]
                );

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công'
            ]);
        }

   public function destroy(Request $request)
        {
            $data = $request->validate([
                'ma_cong_thuc'   => 'required|exists:cong_thuc,ma_cong_thuc',
                'ma_nguyen_lieu' => 'required|exists:nguyen_lieu,ma_nguyen_lieu'
            ]);

            $congThuc = CongThuc::findOrFail($data['ma_cong_thuc']);

            $congThuc->nguyenLieus()->detach($data['ma_nguyen_lieu']);

            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        }

}
