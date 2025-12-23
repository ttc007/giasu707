import sqlite3

from flask import Flask, request, jsonify
from flask_cors import CORS # Thêm dòng này

app = Flask(__name__)
CORS(app) # Thêm dòng này để mở cửa cho Game gửi dữ liệu

DB_NAME = 'leaderboard.db'

# Khởi tạo Database và Bảng
def init_db():
    conn = sqlite3.connect(DB_NAME)
    cursor = conn.cursor()
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS players (
            user_id TEXT PRIMARY KEY,
            name TEXT,
            score INTEGER
        )
    ''')

    # cursor.execute('DELETE FROM players')
    
    print("Database đã được làm sạch!")
    conn.commit()
    conn.close()

@app.route('/save_score', methods=['POST'])
def save_score():
    data = request.json
    u_id = data.get('id')
    u_name = data.get('name')
    u_score = data.get('score')

    if not u_id or not u_name or u_score is None:
        return jsonify({"status": "error"}), 400

    conn = sqlite3.connect(DB_NAME)
    cursor = conn.cursor()
    
    # Sử dụng SQL để "INSERT hoặc UPDATE" (UPSERT)
    # Nếu ID đã tồn tại, chỉ cập nhật nếu điểm mới cao hơn điểm cũ
    cursor.execute('''
        INSERT INTO players (user_id, name, score) 
        VALUES (?, ?, ?)
        ON CONFLICT(user_id) DO UPDATE SET
            name = excluded.name,
            score = MAX(players.score, excluded.score)
    ''', (u_id, u_name, u_score))
    
    conn.commit()
    
    # Lấy Top 10 nhanh chóng bằng SQL
    cursor.execute('SELECT name, score FROM players ORDER BY score DESC LIMIT 5')
    top_10 = [{"name": row[0], "score": row[1]} for row in cursor.fetchall()]
    
    conn.close()
    return jsonify({"status": "success", "leaderboard": top_10})

if __name__ == '__main__':
    init_db()
    app.run(host='0.0.0.0', port=5000)