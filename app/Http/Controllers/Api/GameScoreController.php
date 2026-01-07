<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameScore;
use App\Models\MoveStat;
use DB;

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

        $leaderboard = GameScore::orderBy('score', 'desc')
            ->take(10)
            ->get(['user_name as name', 'score']); 

        // 3. Trả về JSON đúng cấu trúc
        return response()->json([
            'status' => 'success',
            'leaderboard' => $leaderboard
        ]);
    }

    public function updateStats(Request $request)
    {
        $history = $request->input('history', []); // Mảng các move {board, turn, move}
        $result = $request->input('result'); // 'red_win', 'black_win', hoặc 'draw'

        foreach ($history as $item) {
            $turnOfMove = $item['turn']; // 'R' hoặc 'B'
            
            // Xác định cột cần cộng điểm dựa trên phe của nước đi và kết quả trận đấu
            $column = 'draw_count';

            if ($result === 'red_win') {
                $column = ($turnOfMove === 'R') ? 'win_count' : 'lose_count';
            } elseif ($result === 'black_win') {
                $column = ($turnOfMove === 'B') ? 'win_count' : 'lose_count';
            } elseif ($result === 'draw') {
                $column = 'draw_count';
            }

            // Thực hiện Upsert
            MoveStat::upsert(
                [
                    'board' => $item['board'],
                    'turn' => $turnOfMove,
                    'move_text' => $item['move'],
                    'win_count' => ($column === 'win_count' ? 1 : 0),
                    'lose_count' => ($column === 'lose_count' ? 1 : 0),
                    'draw_count' => ($column === 'draw_count' ? 1 : 0),
                ],
                ['board', 'turn', 'move_text'],
                [$column => DB::raw("$column + 1")]
            );
        }

        return response()->json(['message' => 'Stats updated correctly per turn!']);
    }

    public function getAiMove(Request $request) {
        $boardStats = $request->input('board'); // Chuỗi 90 ký tự
        $turn = $request->input('turn');
        $colorForBook = ($turn === 'R') ? 'red' : 'green';

        // --- LẦN 1: TÌM TRỰC TIẾP ---
        $bookMove = DB::table('books')
            ->where('image_chess', $boardStats)
            ->where('color', $colorForBook)
            ->where('is_hidden', 0)
            ->first();

        if ($bookMove) {
            return $this->formatBookResponse($bookMove, false);
        }

        // --- LẦN 2: TÌM ĐỐI XỨNG ---
        $mirroredBoard = $this->mirrorBoard($boardStats);
        $mirrorBookMove = DB::table('books')
            ->where('image_chess', $mirroredBoard)
            ->where('color', $colorForBook)
            ->where('is_hidden', 0)
            ->first();

        if ($mirrorBookMove) {
            return $this->formatBookResponse($mirrorBookMove, true);
        }

        // --- BƯỚC 2: TRUY VẤN TẤT CẢ MOVE_STATS ---
        // Không dùng first(), dùng get() để lấy toàn bộ danh sách nước đi đã học được
        $statMoves = DB::table('move_stats')
            ->where('board', $boardStats)
            ->where('turn', $turn)
            ->get();

        if ($statMoves->isNotEmpty()) {
            $processedMoves = $statMoves->map(function ($item) {
                preg_match('/(\d+),(\d+) to (\d+),(\d+)/', $item->move_text, $matches);
                
                // Công thức tính điểm: Thắng + Hòa*0.5 - Thua
                $score = $item->win_count + ($item->draw_count * 0.5) - $item->lose_count;

                return [
                    'fromCol' => (int)$matches[1],
                    'fromRow' => (int)$matches[2],
                    'toCol' => (int)$matches[3],
                    'toRow' => (int)$matches[4],
                    'score' => $score
                ];
            });

            return response()->json([
                'status' => 'success',
                'source' => 'stats',
                'moves' => $processedMoves // Trả về mảng danh sách nước đi
            ]);
        }

        return response()->json([
                'status' => 'success',
                'source' => 'stats',
                'moves' => [] // Trả về mảng danh sách nước đi
            ]);
    }

    private function mirrorBoard($boardStats) {
        // 1. Chia chuỗi 90 ký tự thành mảng, mỗi phần tử là 1 hàng (9 ô)
        $rows = str_split($boardStats, 9); 
        
        $mirroredRows = array_map(function($row) {
            // 2. Đảo ngược ký tự trong từng hàng (Trái sang Phải)
            return strrev($row);
        }, $rows);
        
        // 3. Nối lại thành chuỗi 90 ký tự đã được đối xứng
        return implode('', $mirroredRows);
    }

    private function formatBookResponse($bookMove, $isMirrored = false) {
        $moveData = json_decode($bookMove->move);
        
        $fromX = (int)$moveData->fromX;
        $toX = (int)$moveData->toX;

        // Nếu là bàn cờ đối xứng, phải đảo ngược tọa độ X (cột)
        if ($isMirrored) {
            $fromX = 10 - $fromX;
            $toX = 10 - $toX;
        }

        return response()->json([
            'status' => 'success',
            'source' => 'book',
            'move' => [
                'fromCol' => -1 + $fromX,
                'fromRow' => 10 - (int)$moveData->fromY,
                'toCol' => -1 + $toX,
                'toRow' => 10 - (int)$moveData->toY
            ],
            'comment' => ($isMirrored ? "[Đối xứng] " : "") . $bookMove->comment
        ]);
    }
}
