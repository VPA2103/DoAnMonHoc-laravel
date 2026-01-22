<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CongThuc;
use App\Models\TheoDoi;
use Illuminate\Http\Request;

class TheoDoiController extends Controller
{

    public function follow(Request $request, $id)
    {
        $user = $request->user();

        if ($user->ma_nguoi_dung == $id) {
            return response()->json([
                'message' => 'Không thể theo dõi chính mình'
            ], 400);
        }

        $daTheoDoi = TheoDoi::where([
            'ma_nguoi_theo_doi' => $user->ma_nguoi_dung,
            'ma_nguoi_duoc_theo_doi' => $id
        ])->exists();

        if ($daTheoDoi) {
            return response()->json([
                'message' => 'Đã theo dõi rồi'
            ], 409);
        }

        TheoDoi::create([
            'ma_nguoi_theo_doi' => $user->ma_nguoi_dung,
            'ma_nguoi_duoc_theo_doi' => $id
        ]);

        return response()->json([
            'message' => 'Theo dõi thành công'
        ]);
    }

    public function unfollow(Request $request, $id)
    {
        $user = $request->user();

        TheoDoi::where([
            'ma_nguoi_theo_doi' => $user->ma_nguoi_dung,
            'ma_nguoi_duoc_theo_doi' => $id
        ])->delete();

        return response()->json([
            'message' => 'Đã bỏ theo dõi'
        ]);
    }

    public function isFollowing($nguoiTheoDoi, $nguoiDuocTheoDoi)
    {
        return TheoDoi::where([
            'ma_nguoi_theo_doi' => $nguoiTheoDoi,
            'ma_nguoi_duoc_theo_doi' => $nguoiDuocTheoDoi
        ])->exists();
    }
}