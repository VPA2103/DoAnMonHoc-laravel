<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => DanhMuc::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'loai' => 'required|in:MON_AN,DIP_LE,CHE_DO'
        ]);

        $danhMuc = DanhMuc::create([
            'ten_danh_muc' => $request->ten_danh_muc,
            'loai' => $request->loai
        ]);

        return response()->json([
            'message' => 'Thêm danh mục thành công',
            'data' => $danhMuc
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $danhMuc = DanhMuc::findOrFail($id);

        $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'loai' => 'required|in:MON_AN,DIP_LE,CHE_DO'
        ]);

        $danhMuc->update([
            'ten_danh_muc' => $request->ten_danh_muc,
            'loai' => $request->loai
        ]);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $danhMuc
        ], 200);
    }

    public function destroy($id)
    {
        $danhMuc = DanhMuc::findOrFail($id);

        // ❌ Không cho xóa nếu còn công thức
        if ($danhMuc->congThucs()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa danh mục đang có công thức'
            ], 400);
        }

        $danhMuc->delete();

        return response()->json([
            'message' => 'Xóa thành công'
        ], 200);
    }
}
