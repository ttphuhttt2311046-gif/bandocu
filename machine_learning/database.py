import pymysql

def get_connection():
    return pymysql.connect(
        host="localhost",
        user="root",
        password="",          # Nếu MySQL có mật khẩu thì điền vào
        database="bandocu18_7",   # Đổi thành tên database thật của bạn
        charset="utf8mb4"
    )