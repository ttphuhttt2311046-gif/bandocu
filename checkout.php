<?php
session_start();
include "db.php";

/* ======================
   KIỂM TRA ĐĂNG NHẬP
====================== */
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập'); window.location='login.php';</script>";
    exit;
}

if (empty($_SESSION['cart'])) {
    echo "<script>alert('Giỏ hàng trống'); window.location='cart.php';</script>";
    exit;
}

$maTaiKhoan = intval($_SESSION['user_id']);

/* ======================
   LẤY THÔNG TIN USER
====================== */
$stmt = $conn->prepare("
    SELECT tenNguoiDung, soDienThoai, diaChi
    FROM taikhoan
    WHERE maTaiKhoan = ?
");
$stmt->bind_param("i", $maTaiKhoan);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ======================
   TÍNH TỔNG TIỀN
====================== */
$tongTien = 0;
foreach ($_SESSION['cart'] as $item) {
    $gia = $item['gia'] ?? $item['donGia'] ?? $item['price'];
    $soLuong = $item['soLuong'] ?? $item['qty'] ?? $item['quantity'] ?? 1;
    $tongTien += $gia * $soLuong;
}

/* ======================
   XỬ LÝ SUBMIT
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $phuongThuc = $_POST['phuongthuc'];

    /* ======================
       1️⃣ TẠO ĐƠN HÀNG
    ====================== */
    $stmt = $conn->prepare("
        INSERT INTO donhang (ngayDat, tongTien, trangThai, maNguoiMua)
        VALUES (NOW(), ?, 'Đang xử lý', ?)
    ");
    $stmt->bind_param("di", $tongTien, $maTaiKhoan);
    $stmt->execute();
    $maDonHang = $stmt->insert_id;
    $stmt->close();

    /* ======================
       2️⃣ CHI TIẾT ĐƠN HÀNG
    ====================== */
    $stmtCT = $conn->prepare("
        INSERT INTO chitietdonhang
        (maDonHang, maSanPham, maNguoiBan, soLuong, donGia, thanhTien)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($_SESSION['cart'] as $item) {

        $maSanPham = $item['maSanPham'] ?? $item['id'];
        $donGia = $item['gia'] ?? $item['donGia'] ?? $item['price'];
        $soLuong = $item['soLuong'] ?? $item['qty'] ?? 1;
        $thanhTien = $donGia * $soLuong;

        // lấy người bán
        $q = $conn->prepare("SELECT maNguoiBan FROM sanpham WHERE maSanPham = ?");
        $q->bind_param("i", $maSanPham);
        $q->execute();
        $maNguoiBan = $q->get_result()->fetch_assoc()['maNguoiBan'];
        $q->close();

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
    }
    $stmtCT->close();

    /* ======================
       3️⃣ THANH TOÁN
    ====================== */
    if ($phuongThuc === 'cod') {

        $stmtTT = $conn->prepare("
            INSERT INTO thanhtoan
            (maDonHang, phuongThuc, soTien, trangThai)
            VALUES (?, 'cod', ?, 'pending')
        ");
        $stmtTT->bind_param("id", $maDonHang, $tongTien);
        $stmtTT->execute();
        $stmtTT->close();

        unset($_SESSION['cart']);
        header("Location: order_success.php");
        exit;
    }

    if ($phuongThuc === 'vnpay') {
        header("Location: vnpay_create_payment.php?order_id=".$maDonHang);
        exit;
    }

    if ($phuongThuc === 'fake_vnpay') {

    $stmtTT = $conn->prepare("
        INSERT INTO thanhtoan
        (maDonHang, phuongThuc, soTien, trangThai)
        VALUES (?, 'fake_vnpay', ?, 'paid')
    ");
    $stmtTT->bind_param("id", $maDonHang, $tongTien);
    $stmtTT->execute();
    $stmtTT->close();

    unset($_SESSION['cart']);
    header("Location: fake_vnpay.php");
    exit;
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
        <p><b>Họ tên:</b> <?= htmlspecialchars($user['tenNguoiDung']) ?></p>
        <p><b>SĐT:</b> <?= htmlspecialchars($user['soDienThoai']) ?></p>
        <p><b>Địa chỉ:</b> <?= htmlspecialchars($user['diaChi']) ?></p>
    </div>

    <hr>

    <h3>Tổng thanh toán</h3>
    <p class="total"><?= number_format($tongTien) ?> VNĐ</p>

    <form method="POST">
        <label><input type="radio" name="phuongthuc" value="cod" checked> Thanh toán khi nhận hàng</label><br><br>
        <label><input type="radio" name="phuongthuc" value="vnpay"> Thanh toán VNPay</label><br><br>
        <label><input type="radio" name="phuongthuc" value="fake_vnpay"> VNPay Demo</label><br><br>

        <button type="submit">XÁC NHẬN THANH TOÁN</button>
        <a href="cart.php">← Quay lại giỏ</a>
       <a href="cart.php" class="quaylai">← Quay lại giỏ hàng</a>

    </form>
</div>

</body>
</html>
