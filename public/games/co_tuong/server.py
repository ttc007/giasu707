from flask import Flask, request, jsonify
from flask_cors import CORS
import sqlite3

app = Flask(__name__)
# Cấu hình CORS chi tiết
CORS(app, resources={r"/*": {"origins": "*"}}, supports_credentials=True)

def init_db():
    conn = sqlite3.connect('xiangqi_ai.db')
    cursor = conn.cursor()
    # board + turn + move_text tạo thành một "khóa duy nhất" (UNIQUE)
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS move_stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            board TEXT,
            turn TEXT,
            move_text TEXT,
            win_count INTEGER DEFAULT 0,
            lose_count INTEGER DEFAULT 0,
            draw_count INTEGER DEFAULT 0,
            UNIQUE(board, turn, move_text)
        )
    ''')
    conn.commit()
    conn.close()

@app.route('/update_stats', methods=['POST'])
def update_stats():
    data = request.json
    history = data.get('history', [])
    result = data.get('result', 'draw') # 'win', 'lose', 'draw'

    conn = sqlite3.connect('xiangqi_ai.db')
    cursor = conn.cursor()

    for item in history:
        # Xác định cột cần cộng điểm
        col_to_update = "win_count" if result == 'win' else ("lose_count" if result == 'lose' else "draw_count")
        
        # Logic: Nếu đã có board+turn+move này rồi thì +1, chưa có thì INSERT mới
        cursor.execute(f'''
            INSERT INTO move_stats (board, turn, move_text, {col_to_update})
            VALUES (?, ?, ?, 1)
            ON CONFLICT(board, turn, move_text) DO UPDATE SET
            {col_to_update} = {col_to_update} + 1
        ''', (item['board'], item['turn'], item['move']))
    
    conn.commit()
    conn.close()
    return jsonify({"status": "success"})

@app.route('/get_ai_move', methods=['POST'])
def get_ai_move():
    data = request.json
    current_board = data.get('board')
    current_turn = data.get('turn')

    conn = sqlite3.connect('xiangqi_ai.db')
    cursor = conn.cursor()

    # Lấy tất cả nước đi khả thi đã có trong DB cho hình cờ này
    cursor.execute('''
        SELECT move_text, win_count, lose_count, draw_count 
        FROM move_stats 
        WHERE board = ? AND turn = ?
    ''', (current_board, current_turn))
    
    results = cursor.fetchall()
    conn.close()

    if not results:
        return jsonify({"status": "no_data"})

    # Thuật toán chọn nước đi tốt nhất:
    # Điểm = Win + (Draw * 0.5) - (Lose * 1)
    best_move = None
    max_score = -99999

    for move_text, win, lose, draw in results:
        score = win + (draw * 0.5) - lose
        if score > max_score:
            max_score = score
            best_move = move_text

    return jsonify({
        "status": "success",
        "move": best_move,
        "score": max_score
    })

if __name__ == '__main__':
    init_db()
    app.run(port=5000)