<?php
session_start();
include "db.php";

/* KIỂM TRA ĐĂNG NHẬP */
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập'); window.location='login.php';</script>";
    exit;
}

$maNguoiDung = intval($_SESSION['user_id']);

/* LẤY GIỎ HÀNG TỪ CSDL */
if (!isset($_SESSION['cart_temp']) || empty($_SESSION['cart_temp'])) {
    echo "<script>alert('Vui lòng chọn sản phẩm'); window.location='cart.php';</script>";
    exit;
}

$selected = $_SESSION['cart_temp'];

// tạo ?,?,?
$placeholders = implode(',', array_fill(0, count($selected), '?'));

// kiểu dữ liệu
$types = str_repeat('i', count($selected) + 1);

// SQL
$sql = "
    SELECT g.maSanPham, g.soLuong, s.gia AS donGia, s.maNguoiBan, s.tenSanPham
    FROM giohang g
    JOIN sanpham s ON g.maSanPham = s.maSanPham
    WHERE g.maNguoiDung = ? AND g.maSanPham IN ($placeholders)
";

$stmtCart = $conn->prepare($sql);

$params = array_merge([$maNguoiDung], $selected);
$stmtCart->bind_param($types, ...$params);

$stmtCart->execute();
$cartUse = $stmtCart->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtCart->close();
if (empty($cartUse)) {
    echo "<script>alert('Giỏ hàng trống'); window.location='cart.php';</script>";
    exit;
}

/*  LẤY THÔNG TIN NGƯỜI DÙNG*/
$stmtUser = $conn->prepare("
    SELECT tenNguoiNhan, soDienThoai, diaChi
FROM diachi
WHERE maTaiKhoan = ?
  AND macDinh = 1
LIMIT 1
");
$stmtUser->bind_param("i", $maNguoiDung);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();
$stmtUser->close();

if (!$user) {
    echo "<script>
        alert('Vui lòng thêm và chọn một địa chỉ mặc định trước khi thanh toán.');
        window.location='caidat/diachi.php?action=add';
    </script>";
    exit;
}

/* TÍNH TỔNG TIỀN */
$tongTien = 0;
foreach ($cartUse as $item) {
    $tongTien += floatval($item['donGia']) * intval($item['soLuong']);
}

/*  XỬ LÝ THANH TOÁN */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phuongThuc = $_POST['phuongthuc'] ?? '';

    if (!in_array($phuongThuc, ['cod','fake_vnpay'])) {
        die("Phương thức thanh toán không hợp lệ");
    }

    $conn->begin_transaction();

    try {
        // TẠO ĐƠN HÀNG
        $stmtDH = $conn->prepare("
    INSERT INTO donhang
    (ngayDat, tongTien, trangThai, maNguoiMua,
     tenNguoiNhan, soDienThoaiNhan, diaChiGiaoHang)
    VALUES (NOW(), ?, 'Chờ thanh toán', ?, ?, ?, ?)
");

$stmtDH->bind_param(
    "disss",
    $tongTien,
    $maNguoiDung,
    $user['tenNguoiNhan'],
    $user['soDienThoai'],
    $user['diaChi']
);
        $stmtDH->execute();
        $maDonHang = $stmtDH->insert_id;
        $stmtDH->close();

        // CHI TIẾT ĐƠN HÀNG
$stmtCT = $conn->prepare("
    INSERT INTO chitietdonhang
    (maDonHang, maSanPham, maNguoiBan, soLuong, donGia, thanhTien)
    VALUES (?, ?, ?, ?, ?, ?)
");

foreach ($cartUse as $item) {

    $maSanPham = intval($item['maSanPham']);
    $maNguoiBan = intval($item['maNguoiBan']);
    $soLuong    = intval($item['soLuong']);
    $donGia     = floatval($item['donGia']);
    $thanhTien  = $donGia * $soLuong;

    // Lưu chi tiết đơn hàng
    $stmtCT->bind_param(
        "iiiidd",
        $maDonHang,
        $maSanPham,
        $maNguoiBan,
        $soLuong,
        $donGia,
        $thanhTien
    );

    $stmtCT->execute();

    // ================= MACHINE LEARNING =================
    $stmtML = $conn->prepare("
        INSERT INTO user_interactions
        (maTaiKhoan, maSanPham, soLanXem, yeuThich, lienHe, muaHang)
        VALUES (?, ?, 0, 0, 0, 1)
        ON DUPLICATE KEY UPDATE
            muaHang = 1
    ");

    $stmtML->bind_param(
        "ii",
        $maNguoiDung,
        $maSanPham
    );

    $stmtML->execute();
    $stmtML->close();
}

$stmtCT->close();

        // THANH TOÁN
        $stmtTT = $conn->prepare("
            INSERT INTO thanhtoan (maDonHang, phuongThuc, soTien, trangThai)
            VALUES (?, ?, ?, 'pending')
        ");
        $stmtTT->bind_param("isd", $maDonHang, $phuongThuc, $tongTien);
        $stmtTT->execute();
        $stmtTT->close();

// XỬ LÝ THEO PHƯƠNG THỨC
if ($phuongThuc === 'cod') {

    // Cập nhật trạng thái đơn hàng
    $conn->query("
        UPDATE donhang
        SET trangThai = 'Đang xử lý'
        WHERE maDonHang = $maDonHang
    ");

    // Cập nhật trạng thái thanh toán
    $conn->query("
        UPDATE thanhtoan
        SET trangThai = 'paid'
        WHERE maDonHang = $maDonHang
    ");

    $conn->commit();
    
// XÓA CHỈ SẢN PHẨM ĐÃ CHỌN
$placeholders = implode(',', array_fill(0, count($selected), '?'));
$typesDel = str_repeat('i', count($selected) + 1);

$sqlDel = "DELETE FROM giohang WHERE maNguoiDung = ? AND maSanPham IN ($placeholders)";
$stmtDel = $conn->prepare($sqlDel);

$paramsDel = array_merge([$maNguoiDung], $selected);
$stmtDel->bind_param($typesDel, ...$paramsDel);

$stmtDel->execute();
$stmtDel->close();

unset($_SESSION['cart_temp']);

            header("Location: order_success.php");
            exit;
        }

        if ($phuongThuc === 'fake_vnpay') {
            $_SESSION['maDonHang'] = $maDonHang;
            $conn->commit();
            header("Location: fake_vnpay.php");
            exit;
        }

    } catch (Exception $e) {
        $conn->rollback();
        die("Lỗi: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thanh toán</title>
<link rel="stylesheet" href="assets/css/checkout.css">
</head>
<body>

<div class="checkout-box">
    <h2>🧾 Thông tin đơn hàng</h2>

    <div class="info">
        <p><b>Họ tên:</b> <?= htmlspecialchars($user['tenNguoiNhan'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><b>SĐT:</b> <?= htmlspecialchars($user['soDienThoai'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><b>Địa chỉ:</b> <?= htmlspecialchars($user['diaChi'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <hr>

    <h3>Tổng thanh toán</h3>
    <p class="total"><?= number_format($tongTien) ?> VNĐ</p>

    <form method="POST">
        <label>
            <input type="radio" name="phuongthuc" value="cod" checked>
            Thanh toán khi nhận hàng
        </label><br><br>

        <label>
            <input type="radio" name="phuongthuc" value="fake_vnpay">
            Thanh toán VNPAY
        </label><br><br>

        <button type="submit">XÁC NHẬN THANH TOÁN</button>
        <a href="cart.php">← Quay lại giỏ hàng</a>
    </form>
</div>

</body>
</html>