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
        $boardStats = $request->input('board'); // Chuỗi 189 ký tự
        $turn = $request->input('turn'); // 'R' hoặc 'B'

        // --- BƯỚC 1: TRUY VẤN BẢNG BOOKS ---
        $boardForBook = $boardStats;
        $colorForBook = ($turn === 'R') ? 'red' : 'green'; // Dựa theo ảnh của bạn dùng 'green'

        $bookMove = DB::table('books')
            ->where('image_chess', $boardForBook)
            ->where('color', $colorForBook)
            ->where('is_hidden', 0)
            ->first();

        if ($bookMove) {
            $moveData = json_decode($bookMove->move);
            return response()->json([
                'status' => 'success',
                'source' => 'book',
                'move' => [
                    'fromCol' => -1 + (int)$moveData->fromX,
                    'fromRow' => 10 - (int)$moveData->fromY, // Đảo ngược lại để khớp Phaser
                    'toCol' => -1 + (int)$moveData->toX,
                    'toRow' => 10 - (int)$moveData->toY    // Đảo ngược lại để khớp Phaser
                ],
                'comment' => $bookMove->comment
            ]);
        }
        // --- BƯỚC 2: TRUY VẤN BẢNG MOVE_STATS ---
        $statMove = DB::table('move_stats')
            ->where('board', $boardStats)
            ->where('turn', $turn)
            ->orderByRaw('(win_count + draw_count * 0.5 - lose_count) DESC')
            ->first();

        if ($statMove) {
            // Parse chuỗi "1,7 to 4,7" thành tọa độ
            preg_match('/(\d+),(\d+) to (\d+),(\d+)/', $statMove->move_text, $matches);
            return response()->json([
                'status' => 'success',
                'source' => 'stats',
                'move' => [
                    'fromCol' => (int)$matches[1],
                    'fromRow' => (int)$matches[2],
                    'toCol' => (int)$matches[3],
                    'toRow' => (int)$matches[4]
                ]
            ]);
        }

        return response()->json(['status' => 'no_data']);
    }

    private function convertToBookFormat($boardStats) {
        // 1. Tách các hàng
        $rows = explode('|', $boardStats);
        
        // 2. ĐẢO NGƯỢC THỨ TỰ HÀNG: Đưa hàng 9 (Đỏ) lên vị trí đầu tiên của chuỗi
        $rows = array_reverse($rows);

        $bookBoard = "";

        // 3. MAP CHUẨN: R (Xe), N (Mã), B (Tượng), A (Sĩ), K (Tướng), C (Pháo), P (Tốt)
        // Đỏ HOA, Đen thường
        $map = [
            'RX' => 'R', 'RM' => 'N', 'RT' => 'B', 'RS' => 'A', 'RG' => 'K', 'RP' => 'C', 'RO' => 'P', // Đỏ
            'BX' => 'r', 'BM' => 'n', 'BT' => 'b', 'BS' => 'a', 'BG' => 'k', 'BP' => 'c', 'BO' => 'p'  // Đen
        ];

        foreach ($rows as $row) {
            for ($i = 0; $i < strlen($row); $i += 2) {
                $piece = substr($row, $i, 2);
                $bookBoard .= $map[$piece] ?? ".";
            }
        }
        
        return $bookBoard;
    }
}
