<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function danhSachDangTheoDoi()
    {
        $userId = auth()->id();

        $data = TheoDoi::with('nguoiDuocTheoDoi')
            ->where('ma_nguoi_theo_doi', $userId)
            ->get();

        return response()->json($data);
    }

    public function danhSachNguoiTheoDoi()
{
    $userId = auth()->id();

    $data = TheoDoi::with('nguoiTheoDoi')
        ->where('ma_nguoi_duoc_theo_doi', $userId)
        ->get()
        ->map(function ($item) {
            return [
                'ma_nguoi_dung' => $item->nguoiTheoDoi->ma_nguoi_dung,
                'ten_nguoi_dung' => $item->nguoiTheoDoi->ten_nguoi_dung,
                'username' => $item->nguoiTheoDoi->username,
                'anh_dai_dien' => $item->nguoiTheoDoi->anh_dai_dien,
                'da_theo_doi' => TheoDoi::where([
                    'ma_nguoi_theo_doi' => auth()->id(),
                    'ma_nguoi_duoc_theo_doi' => $item->nguoiTheoDoi->ma_nguoi_dung
                ])->exists()
            ];
        });

    return response()->json($data);
}


     public function checkFollowing($maNguoiDuocTheoDoi)
    {
        $userId = auth()->id();

        $isFollowing = TheoDoi::where([
            'ma_nguoi_theo_doi' => $userId,
            'ma_nguoi_duoc_theo_doi' => $maNguoiDuocTheoDoi
        ])->exists();

        return response()->json([
            'is_following' => $isFollowing
        ]);
    }
}