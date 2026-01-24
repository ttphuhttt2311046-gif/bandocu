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
$sqlUser = $conn->prepare("
    SELECT tenNguoiDung, soDienThoai, diaChi
    FROM taikhoan
    WHERE maTaiKhoan = ?
");
$sqlUser->bind_param("i", $maTaiKhoan);
$sqlUser->execute();
$user = $sqlUser->get_result()->fetch_assoc();
$sqlUser->close();

/* ======================
   TÍNH TỔNG TIỀN
   ====================== */
$tongTien = 0;
foreach ($_SESSION['cart'] as $item) {

    $gia = 0;
    $soLuong = 1;

    if (isset($item['gia'])) $gia = $item['gia'];
    if (isset($item['donGia'])) $gia = $item['donGia'];
    if (isset($item['price'])) $gia = $item['price'];

    if (isset($item['soLuong'])) $soLuong = $item['soLuong'];
    if (isset($item['qty'])) $soLuong = $item['qty'];
    if (isset($item['quantity'])) $soLuong = $item['quantity'];

    $tongTien += $gia * $soLuong;
}

/* ======================
   XỬ LÝ SUBMIT
   ====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $phuongThuc = $_POST['phuongthuc'];

    // Tạo đơn hàng (chưa thanh toán)
    $stmt = $conn->prepare("
        INSERT INTO donhang (ngayDat, tongTien, trangThai, maNguoiMua)
        VALUES (NOW(), ?, 0, ?)
    ");
    $stmt->bind_param("di", $tongTien, $maTaiKhoan);
    $stmt->execute();
    $maDonHang = $stmt->insert_id;
    $stmt->close();

    $_SESSION['maDonHang'] = $maDonHang;

    if ($phuongThuc === 'cod') {
        header("Location: order_success.php");
        exit;
    }

    if ($phuongThuc === 'vnpay') {
        header("Location: vnpay_create_payment.php?order_id=".$maDonHang);
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
        <div class="pay-method">
            <label>
                <input type="radio" name="phuongthuc" value="cod" checked>
                Thanh toán khi nhận hàng
            </label><br><br>
            <label>
                <input type="radio" name="phuongthuc" value="vnpay">
                Thanh toán qua VNPay
            </label>
        </div>

        <button type="submit">XÁC NHẬN THANH TOÁN</button>
       <a href="cart.php" class="quaylai">← Quay lại giỏ hàng</a>

    </form>
</div>

</body>
</html>
