<?php

namespace App\Http\Controllers\Api;
use App\Models\CauHoi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TraLoi;
class CauHoiController extends Controller
{
    //  User gửi câu hỏi
    public function store(Request $request)
    {
        $request->validate([
            'noi_dung' => 'required|string'
        ]);

        $cauHoi = CauHoi::create([
            'noi_dung' => $request->noi_dung,
            'ma_nguoi_dung' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi câu hỏi thành công',
            'data' => $cauHoi
        ], 201);
    }

    //  User xem câu hỏi của chính mình
    public function myQuestions()
    {
        $userId = Auth::id();

        $cauHois = CauHoi::where('ma_nguoi_dung', $userId)
            ->orderBy('ma_cau_hoi', 'desc')
            ->get();

        // load thủ công câu trả lời
        foreach ($cauHois as $cauHoi) {
            $cauHoi->tra_lois = TraLoi::where(
                'ma_cau_hoi',
                $cauHoi->ma_cau_hoi
            )->get();
        }

        return response()->json([
            'success' => true,
            'data' => $cauHois
        ]);
    }

    public function adminIndex()
    {
        $cauHois = CauHoi::with([
            'nguoiDung:ma_nguoi_dung,ten_nguoi_dung',
            'traLois'
        ])
        ->orderBy('ma_cau_hoi', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $cauHois
        ]);
    }


    public function adminTraLoi(Request $request, $maCauHoi)
    {
        $request->validate([
            'noi_dung' => 'required'
        ]);

        $traLoi = TraLoi::create([
            'noi_dung' => $request->noi_dung,
            'ma_cau_hoi' => $maCauHoi,
            'ma_nguoi_dung' => Auth::id(), // admin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin trả lời thành công',
            'data' => $traLoi
        ]);
    }
}
