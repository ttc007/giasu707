import sqlite3

def check_data():
    try:
        conn = sqlite3.connect('xiangqi_ai.db')
        cursor = conn.cursor()
        
        # 1. Kiểm tra tổng số nước đi đã lưu
        cursor.execute("SELECT COUNT(*) FROM move_stats")
        total = cursor.fetchone()[0]
        print(f"--- TỔNG SỐ BẢN GHI: {total} ---")

        # 2. Lấy 5 nước đi có tỷ lệ thắng cao nhất để xem thử
        print("\n--- TOP 5 NƯỚC ĐI CÓ KINH NGHIỆM CHIẾN THẮNG ---")
        cursor.execute('''
            SELECT board, turn, move_text, win_count, lose_count, draw_count 
            FROM move_stats 
            ORDER BY win_count DESC 
            LIMIT 5
        ''')
        
        rows = cursor.fetchall()
        for row in rows:
            print(f"Phe: {row[1]} | Nước đi: {row[2]} | Thắng: {row[3]} | Thua: {row[4]} | Hòa: {row[5]}")
            # print(f"Hình cờ: {row[0][:30]}...") # Chỉ in một đoạn đầu của chuỗi board

        conn.close()
    except Exception as e:
        print(f"Lỗi khi đọc DB: {e}")

if __name__ == '__main__':
    check_data()