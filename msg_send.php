<?php
session_start();
if (!isset($_SESSION['last_product_id'])) {
require_once 'admin/validation.php';
$_SESSION['last_product_id'] = 0;

$_SESSION['last_product_name'] = '';}
require 'db.php';
require_once 'gemini.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'error'=>'Chưa đăng nhập']);
    exit;
}

$sender   = (int)$_SESSION['user_id'];
$receiver = (int)($_POST['receiver_id'] ?? 0);
$message  = clean($_POST['message'] ?? '');
$detailWords = [

 'ok', 'ừ', 'uh', 'yes',

'xem chi tiết', 'chi tiết', 'gửi link', 'xem đi'

];

$msgLower = mb_strtolower($message, 'UTF-8');

$isDetailRequest = false;

foreach ($detailWords as $word) {
    if (mb_strpos($msgLower, $word) !== false) {
        $isDetailRequest = true;
        break;
    }
}

if ($isDetailRequest && $_SESSION['last_product_id'] > 0) {

    $link = "http://localhost/bandocu2/product.php?id=" .
            $_SESSION['last_product_id'];

    $reply = "🔗 Đây là link sản phẩm "
           . $_SESSION['last_product_name']
           . "\n" . $link;

    $botId = 9999;

    $stmt = $conn->prepare("
        INSERT INTO nhantin
        (noiDung, maNguoiGui, maNguoiNhan, trangThai, ngayGui)
        VALUES (?, ?, ?, 'chua_xem', NOW())
    ");

    $stmt->bind_param("sii", $reply, $botId, $sender);
    $stmt->execute();

    echo json_encode(['success'=>true]);
    exit;
}
$detailWords = [
    'ok',
    'ừ',
    'uh',
    'yes',
    'chi tiết',
    'xem chi tiết',
    'gửi link',
    'link sản phẩm',
    'xem thêm',
    'xem đi'
];

foreach ($detailKeywords as $keyword) {
    if (stripos($message, $keyword) !== false &&
        !empty($_SESSION['last_question'])) {

        $message = $_SESSION['last_question'];
        break;
    }
}
if (!isset($_SESSION['last_question'])) {
    $_SESSION['last_question'] = '';
}

if ($receiver <= 0 || $message === '') {
    echo json_encode(['success'=>false,'error'=>'Dữ liệu không hợp lệ']);
    exit;
}

/* ================= HÀM PHỤ ================= */

function detectIntent($msg){
    $msg = strtolower($msg);

    if (strpos($msg, 'rẻ nhất') !== false) return 'cheap';
    if (strpos($msg, 'đắt nhất') !== false) return 'expensive';
    if (strpos($msg, 'tư vấn') !== false) return 'advice';
    return 'normal';
}

function extractPriceRange($text) {

    $text = strtolower($text);
    $text = str_replace(['triệu','tr'], '000000', $text);
    $text = str_replace(['k','nghìn'], '000', $text);

    $min = 0;
    $max = 0;

    if (preg_match('/dưới\s*(\d+)/', $text, $m)) {
        $max = (int)$m[1];
    }

    if (preg_match('/trên\s*(\d+)/', $text, $m)) {
        $min = (int)$m[1];
    }

    if (preg_match('/(\d+)\s*(đến|-)\s*(\d+)/', $text, $m)) {
        $min = (int)$m[1];
        $max = (int)$m[3];
    }

    return [$min, $max];
}

/* ================= AI CHAT ================= */
if ($receiver == 9999) {

    /* ===== 1. LƯU USER → AI ===== */
    $stmt = $conn->prepare("
        INSERT INTO nhantin (noiDung, maNguoiGui, maNguoiNhan, trangThai, ngayGui)
        VALUES (?, ?, ?, 'chua_xem', NOW())
    ");
    $stmt->bind_param("sii", $message, $sender, $receiver);
    $stmt->execute();

    /* ===== 2. PHÂN TÍCH ===== */
    $intent = detectIntent($message);
    list($minPrice, $maxPrice) = extractPriceRange($message);

    /* ===== 3. TẠO QUERY ===== */
    $conditions = [];    
    $params = [];
    $types = "";

$stopWords = [
'có','không','tôi','muốn',
'cần','shop','cho',
'xin','à','ạ','ơi','giúp',
'bạn','em','anh','chị',
'còn','hay','với',
'là','được','loại',
'này',
'kia',
'đó',
'đây',
'nữa',
'thế',
'vậy'
];
$message = preg_replace('/\bcó\b/ui', '', $message);
$message = preg_replace('/\bkhông\b/ui', '', $message);
$message = preg_replace('/\bk\b/ui', '', $message);
$message = preg_replace('/\bcòn\b/ui', '', $message);

$message = trim($message);
$searchText = mb_strtolower(trim($message), 'UTF-8');

$words = explode(" ", $searchText);

// Tìm theo cả cụm từ trước
$conditions[] = "(tenSanPham LIKE ? OR moTa LIKE ?)";
$params[] = "%$searchText%";
$params[] = "%$searchText%";
$types .= "ss";

// Sau đó mới tách từng từ
foreach ($words as $w){
    $w = trim($w);

    if(mb_strlen($w, 'UTF-8') < 2 || in_array($w,$stopWords)) {
        continue;
    }

    $conditions[] = "(tenSanPham LIKE ? OR moTa LIKE ?)";
    $params[] = "%$w%";
    $params[] = "%$w%";
    $types .= "ss";
}

    if ($minPrice > 0){
        $conditions[] = "gia >= ?";
        $params[] = $minPrice;
        $types .= "i";
    }

    if ($maxPrice > 0){
        $conditions[] = "gia <= ?";
        $params[] = $maxPrice;
        $types .= "i";
    }

    $order = "";
    if ($intent == 'cheap') $order = " ORDER BY gia ASC";
    if ($intent == 'expensive') $order = " ORDER BY gia DESC";

  $sql = "SELECT maSanPham, tenSanPham, gia, moTa

FROM sanpham

WHERE trangThai = 1";
if (!empty($conditions)) {

   $sql .= " AND ("
      . implode(" OR ", $conditions)
      . ")";
}

$sql .= $order . " LIMIT 20";    
$stmt = $conn->prepare($sql);

    if (!empty($params)){
        $stmt->bind_param($types, ...$params);
    }

   
$stmt->execute();
$res = $stmt->get_result();

file_put_contents(
    "debug.txt",
    "SQL: $sql\n" .
    "PARAMS: " . print_r($params, true) . "\n" .
    "ROWS: " . $res->num_rows . "\n\n",
    FILE_APPEND
);
    /* ===== 4. LỊCH SỬ CHAT ===== */
    $history = "";
    $his = $conn->query("
        SELECT noiDung, maNguoiGui 
        FROM nhantin 
        WHERE (maNguoiGui = $sender AND maNguoiNhan = 9999)
           OR (maNguoiGui = 9999 AND maNguoiNhan = $sender)
        ORDER BY ngayGui DESC
        LIMIT 5
    ");

    while ($row = $his->fetch_assoc()) {
        $role = $row['maNguoiGui']==$sender ? "Khách" : "AI";
        $history = "$role: {$row['noiDung']}\n" . $history;
    }

    /* ===== 5. DATA CHO AI ===== */
/* ===== 5. DATA CHO AI ===== */
$products = "";

$productList = [];

$first = true;

while ($row = $res->fetch_assoc()) {

    $gia = number_format($row['gia'],0,',','.');

$link = "http://localhost/bandocu1/product.php?id=".$row['maSanPham'];

$products .=
"- {$row['tenSanPham']} ({$gia}đ)\n".
"  Mô tả: {$row['moTa']}\n".
"  Link: {$link}\n\n";

    $productList[] = $row;

    if ($first) {

        $_SESSION['last_product_id'] = $row['maSanPham'];
        $_SESSION['last_product_name'] = $row['tenSanPham'];

        $first = false;
    }
} // <-- đóng while ở đây

file_put_contents("products.txt", $products);

if ($products == "") {

    $reply = "Dạ hiện tại shop chưa có sản phẩm phù hợp với nhu cầu của anh/chị ạ.";

    $stmt = $conn->prepare("
        INSERT INTO nhantin
        (noiDung, maNguoiGui, maNguoiNhan, trangThai, ngayGui)
        VALUES (?, ?, ?, 'chua_xem', NOW())
    ");

    $stmt->bind_param("sii", $reply, $receiver, $sender);
    $stmt->execute();

    echo json_encode(['success'=>true]);
    exit;
}

      
    

    $prompt = "
Bạn là nhân viên bán hàng chuyên nghiệp.

Sản phẩm:
$products

Yêu cầu:
- Chỉ dùng sản phẩm trong danh sách.
- Khi giới thiệu sản phẩm phải ghi luôn link của sản phẩm.
- Không tạo link mới.
- Không tách riêng phần danh sách sản phẩm.
- Trả lời tự nhiên như nhân viên bán hàng.

Câu hỏi: $message
";

    /* ===== 7. GỌI AI ===== */
$aiReply = callGemini($prompt);

$reply = $aiReply;

$_SESSION['last_question'] = $message;
    /* ===== 8. LƯU AI → USER ===== */
    $stmt = $conn->prepare("
        INSERT INTO nhantin (noiDung, maNguoiGui, maNguoiNhan, trangThai, ngayGui)
        VALUES (?, ?, ?, 'chua_xem', NOW())
    ");
    $stmt->bind_param("sii", $reply, $receiver, $sender);
    $stmt->execute();

    echo json_encode(['success'=>true]);
    exit;
}

/* ================= CHAT THƯỜNG ================= */
$stmt = $conn->prepare("
    INSERT INTO nhantin (noiDung, maNguoiGui, maNguoiNhan, trangThai, ngayGui)
    VALUES (?, ?, ?, 'chua_xem', NOW())
");

$stmt->bind_param("sii", $message, $sender, $receiver);
$ok = $stmt->execute();

echo json_encode([
    'success' => $ok,
    'error'   => $ok ? null : $conn->error
]);