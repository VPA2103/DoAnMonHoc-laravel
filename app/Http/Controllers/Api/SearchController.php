<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CongThuc;
use App\Models\KeHoachBuaAn;
use App\Models\DanhMuc;
use App\Models\Blog;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->query('q');

        if (!$q) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cong_thuc' => [],
                    'ke_hoach' => [],
                    'danh_muc' => [],
                    'blog' => [],
                ]
            ]);
        }

        // 🍳 Công thức
        $congThuc = CongThuc::where('trang_thai', 2)
            ->where('ten_cong_thuc', 'like', "%$q%")
            ->limit(5)
            ->get([
                'ma_cong_thuc',
                'ten_cong_thuc',
                'slug'
            ]);

        // 📅 Kế hoạch
        $keHoach = KeHoachBuaAn::where('ghi_chu', 'like', "%$q%")
            ->orWhere('ngay', 'like', "%$q%")
            ->limit(5)
            ->get([
                'ma_ke_hoach',
                'ngay',
                'ghi_chu'
            ]);

        // 📂 Danh mục
        $danhMuc = DanhMuc::where('ten_danh_muc', 'like', "%$q%")
            ->limit(5)
            ->get([
                'ma_danh_muc',
                'ten_danh_muc'
            ]);

        // 📰 Blog (CHỈ LẤY TÊN)
        $blog = Blog::where('tieu_de', 'like', "%$q%")
            ->limit(5)
            ->get([
                'ma_blog',
                'tieu_de',
                'slug'
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'cong_thuc' => $congThuc,
                'ke_hoach' => $keHoach,
                'danh_muc' => $danhMuc,
                'blog' => $blog,
            ]
        ]);
    }

}
