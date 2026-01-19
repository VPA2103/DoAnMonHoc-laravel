<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function blogs(Request $request)
    {
        $userId = $request->user()->ma_nguoi_dung;

        $blogs = DB::table('blog')
            ->join('nguoi_dung', 'blog.ma_nguoi_dung', '=', 'nguoi_dung.ma_nguoi_dung')
            ->whereIn('blog.ma_nguoi_dung', function ($query) use ($userId) {
                $query->select('ma_nguoi_duoc_theo_doi')
                    ->from('theo_doi')
                    ->where('ma_nguoi_theo_doi', $userId);
            })
            ->select(
                'blog.ma_blog',
                'blog.tieu_de',
                'blog.slug',
                'blog.noi_dung',
                'blog.trang_thai',
                'blog.created_at',
                'nguoi_dung.ten_nguoi_dung',
                'nguoi_dung.anh_dai_dien'
            )
            ->orderBy('blog.created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data'   => $blogs
        ]);
    }
}