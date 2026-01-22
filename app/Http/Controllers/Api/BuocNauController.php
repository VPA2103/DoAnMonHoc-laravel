<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuocNau;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BuocNauController extends Controller
{
    // 1. Thêm bước nấu mới
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ma_cong_thuc' => 'required|exists:cong_thuc,ma_cong_thuc',
            'so_thu_tu'     => 'required|integer|min:1',
            'noi_dung'     => 'required|string',
            'hinh_anh'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thoi_gian'    => 'nullable|integer|min:0', // ✅ BẮT BUỘC

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['ma_cong_thuc', 'so_thu_tu', 'noi_dung']);

        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('buoc-nau', 'public');
            $data['hinh_anh'] = $path;
        }

        $buocNau = BuocNau::create($data);

        return response()->json([
            'message' => 'Thêm bước nấu thành công',
            'data'    => $buocNau
        ], 201);
    }

    // 2. Sửa bước nấu (đã fix validate đúng tên field)
    public function update(Request $request, $id)
    {
        $buocNau = BuocNau::find($id);

        if (!$buocNau) {
            return response()->json(['message' => 'Không tìm thấy bước nấu'], 404);
        }

        $validator = Validator::make($request->all(), [
            'so_thu_tu' => 'integer|min:1',
            'noi_dung'  => 'string',
            'hinh_anh'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thoi_gian'    => 'nullable|integer|min:0', // them cot thoi gian

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['so_thu_tu', 'noi_dung','thoi_gian' ]);

        if ($request->hasFile('hinh_anh')) {
            // Xóa ảnh cũ nếu có
            if ($buocNau->hinh_anh && Storage::disk('public')->exists($buocNau->hinh_anh)) {
                Storage::disk('public')->delete($buocNau->hinh_anh);
            }
            $path = $request->file('hinh_anh')->store('buoc-nau', 'public');
            $data['hinh_anh'] = $path;
        }

        $buocNau->update($data);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data'    => $buocNau->fresh()
        ]);
    }

    // 3. Xóa bước nấu
    public function destroy($id)
    {
        $buocNau = BuocNau::find($id);

        if (!$buocNau) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        if ($buocNau->hinh_anh && Storage::disk('public')->exists($buocNau->hinh_anh)) {
            Storage::disk('public')->delete($buocNau->hinh_anh);
        }

        $buocNau->delete();

        return response()->json(['message' => 'Xóa bước nấu thành công']);
    }

    // 4. Lấy danh sách bước theo công thức
    public function getStepsByRecipeId($id)
    {
        $steps = BuocNau::where('ma_cong_thuc', $id)
                        ->orderBy('so_thu_tu', 'asc')
                        ->get();

        return response()->json($steps);
    }
}