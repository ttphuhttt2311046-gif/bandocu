from database import get_connection

try:
    conn = get_connection()
    print("Kết nối MySQL thành công!")
    conn.close()
except Exception as e:
    print("Lỗi:", e)