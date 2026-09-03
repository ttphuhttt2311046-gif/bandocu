import pandas as pd
import joblib

from sklearn.ensemble import RandomForestClassifier

from database import get_connection

# ==========================
# Kết nối MySQL
# ==========================
conn = get_connection()

# ==========================
# Lấy dữ liệu huấn luyện
# ==========================

query = """
SELECT
    ui.maTaiKhoan,
    ui.maSanPham,
    ui.soLanXem,
    ui.yeuThich,
    ui.lienHe,
    ui.muaHang,

    sp.maDanhMuc,
    sp.gia,
    sp.luotXem

FROM user_interactions ui

JOIN sanpham sp
ON ui.maSanPham = sp.maSanPham
"""

df = pd.read_sql(query, conn)

conn.close()

print("===== DỮ LIỆU =====")
print(df.head())

print("\nKích thước:", df.shape)

# ==========================
# Kiểm tra dữ liệu
# ==========================

if df.empty:
    print("Không có dữ liệu để train.")
    exit()

# ==========================
# Tạo nhãn
# ==========================

df["target"] = (
    (df["muaHang"] == 1)
    |
    (df["yeuThich"] == 1)
).astype(int)

# ==========================
# Feature
# ==========================

X = df[
    [
        "maDanhMuc",
        "gia",
        "luotXem",
        "soLanXem",
        "yeuThich",
        "lienHe"
    ]
]

y = df["target"]

# ==========================
# Train Random Forest
# ==========================

model = RandomForestClassifier(
    n_estimators=100,
    random_state=42
)

model.fit(X, y)

# ==========================
# Lưu model
# ==========================

joblib.dump(model, "model.pkl")

print("\n========================")
print("Train thành công!")
print("Model đã lưu: model.pkl")
print("========================")