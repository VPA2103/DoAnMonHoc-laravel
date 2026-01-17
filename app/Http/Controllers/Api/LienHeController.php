<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LienHe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LienHeController extends Controller
{
    public function guiLienHe(Request $request)
    {
        $data = $request->validate([
            'ho_ten'   => 'required|string|max:255',
            'email'   => 'required|email',
            'tieu_de' => 'required|string|max:255',
            'noi_dung'=> 'required|string',
        ]);

        $data['ma_nguoi_dung'] = Auth::guard('api')->user()
            ? Auth::guard('api')->id()
            : null;

        $data['ngay_gui'] = now();
        $data['trang_thai'] = 'moi';

        LienHe::create($data);

        return response()->json([
            'message' => 'Gửi liên hệ thành công'
        ], 201);
    }
    
     public function danhSachLienHe(Request $request)
    {
        $query = LienHe::query();

        // lọc theo trạng thái (nếu có)
        if ($request->has('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // sắp xếp mới nhất lên đầu
        $lienHe = $query->orderBy('ngay_gui', 'desc')->paginate(10);

        return response()->json($lienHe, 200);
    }

    public function xoaLienHe($id)
    {
        $lienHe = LienHe::find($id);

        if (!$lienHe) {
            return response()->json([
                'message' => 'Liên hệ không tồn tại'
            ], 404);
        }

        $lienHe->delete();

        return response()->json([
            'message' => 'Xóa liên hệ thành công'
        ], 200);
    }

}
