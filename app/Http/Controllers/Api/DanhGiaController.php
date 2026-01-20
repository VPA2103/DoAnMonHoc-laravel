<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use Illuminate\Http\Request;
use App\Models\CongThuc;
use Illuminate\Support\Facades\Auth;

class DanhGiaController extends Controller
{
    //  Lấy danh sách đánh giá  Admin / test / thống kê
    public function index()
    {
        $danhGias = DanhGia::with([
            'nguoiDung:ma_nguoi_dung,ten_nguoi_dung',
            'congThuc:ma_cong_thuc,ten_cong_thuc'
        ])->orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'data' => $danhGias
        ]);
    }

    // Lấy đánh giá,hàm này hiển thị đánh giá của 1 bài đăng 
    public function theoCongThuc($ma_cong_thuc)
    {
        $danhGias = DanhGia::with([
            'nguoiDung:ma_nguoi_dung,ten_nguoi_dung'
        ])->where('ma_cong_thuc', $ma_cong_thuc)->get();

        return response()->json([
            'success' => true,
            'data' => $danhGias
        ]);
    }
    //gui danh gia
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        $request->validate([
            'ma_cong_thuc' => 'required|exists:cong_thuc,ma_cong_thuc',
            'so_sao' => 'required|integer|min:1|max:5',
        ]);

        $congThuc = CongThuc::find($request->ma_cong_thuc);

        //  Không cho tự đánh giá
        if ($congThuc->ma_nguoi_dung == $user->ma_nguoi_dung) {
            return response()->json([
                'message' => 'Bạn không thể đánh giá công thức của chính mình'
            ], 403);
        }

        //  Không cho đánh giá lại
        $daDanhGia = DanhGia::where('ma_cong_thuc', $request->ma_cong_thuc)
            ->where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->exists();

        if ($daDanhGia) {
            return response()->json([
                'message' => 'Bạn đã đánh giá công thức này rồi'
            ], 409);
        }

        // Tạo đánh giá
        DanhGia::create([
            'ma_cong_thuc' => $request->ma_cong_thuc,
            'ma_nguoi_dung' => $user->ma_nguoi_dung,
            'so_sao' => $request->so_sao,
        ]);

        return response()->json([
            'message' => 'Đánh giá thành công'
        ]);
    }


    // Danh sách đánh giá của user đang đăng nhập  “Tao đã đi đánh giá những bài nào?”
    public function danhSachDanhGiaCuaToi()
    {
        $user = Auth::guard('api')->user();

        $data = DanhGia::with('congThuc:ma_cong_thuc,ten_cong_thuc')
            ->where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    //SỬA ĐÁNH GIÁ (user sửa số sao)
    public function update(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        $request->validate([
            'so_sao' => 'required|integer|min:1|max:5',
        ]);

        $danhGia = DanhGia::where('id', $id)
            ->where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->firstOrFail();

        $danhGia->update([
            'so_sao' => $request->so_sao
        ]);

        return response()->json([
            'message' => 'Cập nhật đánh giá thành công'
        ]);
    }

    //XÓA ĐÁNH GIÁ (chỉ chủ mới được xóa)
    public function destroy($id)
    {
        $user = Auth::guard('api')->user();

        $danhGia = DanhGia::where('id', $id)
            ->where('ma_nguoi_dung', $user->ma_nguoi_dung)
            ->firstOrFail();

        $danhGia->delete();

        return response()->json([
            'message' => 'Xóa đánh giá thành công'
        ]);
    }

    //THỐNG KÊ SAO CHO 1 BÀI
    public function thongKeTheoCongThuc($ma_cong_thuc)
    {
        $data = DanhGia::where('ma_cong_thuc', $ma_cong_thuc);

        return response()->json([
            'avg_star' => round($data->avg('so_sao'), 1),
            'total' => $data->count()
        ]);
    }
}