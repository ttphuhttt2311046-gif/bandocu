import warnings
warnings.filterwarnings("ignore")

import sys

sys.stdout.reconfigure(encoding="utf-8")

import sys
import json
import os
import joblib
import pandas as pd

from database import get_connection

# ======================
# Kiểm tra tham số
# ======================
if len(sys.argv) < 2:
    print(json.dumps([]))
    exit()

maTaiKhoan = int(sys.argv[1])

# ======================
# Load model
# ======================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "model.pkl")

model = joblib.load(MODEL_PATH)

# ======================
# Kết nối MySQL
# ======================
conn = get_connection()

# ======================
# Lấy dữ liệu
# ======================
query = """
SELECT
    sp.maSanPham,
    sp.tenSanPham,
    sp.maDanhMuc,
    sp.gia,
    sp.luotXem,

    COALESCE(ui.soLanXem,0) AS soLanXem,
    COALESCE(ui.yeuThich,0) AS yeuThich,
    COALESCE(ui.lienHe,0) AS lienHe

FROM sanpham sp

LEFT JOIN user_interactions ui
ON sp.maSanPham = ui.maSanPham
AND ui.maTaiKhoan = %s
"""

df = pd.read_sql(query, conn, params=[maTaiKhoan])

conn.close()

if df.empty:
    print(json.dumps([]))
    exit()

# ======================
# Feature
# ======================
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

# ======================
# Predict
# ======================
df["score"] = model.predict_proba(X)[:, 1]

# ======================
# Top 5
# ======================
result = (
    df.sort_values("score", ascending=False)
      .head(5)[["maSanPham", "tenSanPham", "score"]]
)

print(result.to_json(
    orient="records",
    force_ascii=False
))