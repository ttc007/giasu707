<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameScore;

class GameScoreController extends Controller
{
    public function saveScore(Request $request)
    {
        // 1. Lưu hoặc cập nhật điểm
        $scoreEntry = GameScore::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'user_name' => $request->user_name,
                'score' => $request->score
            ]
        );

        // 2. Lấy danh sách xếp hạng (Top 10)
        // Đổi tên cột 'user_name' thành 'name' để khớp với yêu cầu map('name') của bạn
        $leaderboard = GameScore::orderBy('score', 'desc')
            ->take(10)
            ->get(['user_name as name', 'score']); 

        // 3. Trả về JSON đúng cấu trúc
        return response()->json([
            'status' => 'success',
            'leaderboard' => $leaderboard
        ]);
    }
}