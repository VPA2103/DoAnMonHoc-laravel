<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguyenLieu;
use Illuminate\Http\Request;

class NguyenLieuController extends Controller
{
    /**
     * GET /api/nguyen-lieu
     */
    public function index(Request $request)
    {
        $query = NguyenLieu::query();

        if ($request->filled('ma_cong_thuc')) {
            $query->where('ma_cong_thuc', $request->ma_cong_thuc);
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('ma_nguyen_lieu', 'desc')->get()
        ]);
    }

    /**
     * GET /api/nguyen-lieu/{id}
     */
    public function show($id)
    {
        $nguyenLieu = NguyenLieu::where('ma_nguyen_lieu', $id)->first();

        if (!$nguyenLieu) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nguyên liệu'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $nguyenLieu
        ]);
    }
}
