<?php
require_once "db.php";
require_once "gemini.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$message = strtolower(trim($data['message'] ?? ''));

if (!$message) {
    echo json_encode(["reply" => "Bạn chưa nhập câu hỏi"]);
    exit;
}

/* ===== 1. TÌM SẢN PHẨM TRỰC TIẾP ===== */
$sql = "SELECT * FROM sanpham 
        WHERE trangThai = 1 
        AND tenSanPham LIKE '%$message%' 
        LIMIT 5";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $reply = "Shop có các sản phẩm sau:\n";

    while ($row = $res->fetch_assoc()) {
        $reply .= "- {$row['tenSanPham']} ({$row['gia']}đ)\n";
    }

    echo json_encode(["reply" => $reply]);
    exit;
}

/* ===== 2. LẤY DANH SÁCH SẢN PHẨM ===== */
$products = "";
$res = $conn->query("SELECT tenSanPham, gia, moTa FROM sanpham WHERE trangThai = 1 LIMIT 20");

while ($row = $res->fetch_assoc()) {
    $products .= "- {$row['tenSanPham']} ({$row['gia']}đ): {$row['moTa']}\n";
}

/* ===== 3. PROMPT CHUẨN ===== */
$prompt = "
Bạn là nhân viên tư vấn bán hàng.

Danh sách sản phẩm:
$products

Quy tắc:
- Trả lời ngắn gọn
- Không bịa sản phẩm
- Không có thì nói 'Shop chưa có sản phẩm này'

Câu hỏi: $message
";

/* ===== 4. GỌI AI ===== */
$reply = callGemini($prompt);

echo json_encode(["reply" => $reply]);