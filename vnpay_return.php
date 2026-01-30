<?php
session_start();
include "db.php";

$vnp_HashSecret = "Y7GFSRHIF6NMF42LOAI116GNW6ZTXQ0R"; // giống file create

/* ======================
   KIỂM TRA DỮ LIỆU TRẢ VỀ
   ====================== */
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
unset($_GET['vnp_SecureHash']);
unset($_GET['vnp_SecureHashType']);

ksort($_GET);
$hashData = "";

foreach ($_GET as $key => $value) {
    $hashData .= ($hashData ? '&' : '') . urlencode($key) . "=" . urlencode($value);
}

$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

/* ======================
   XÁC THỰC CHỮ KÝ
   ====================== */
if ($secureHash !== $vnp_SecureHash) {
    die("❌ Chữ ký không hợp lệ");
}

/* ======================
   XỬ LÝ KẾT QUẢ
   ====================== */
$maDonHang = intval($_GET['vnp_TxnRef']);
$responseCode = $_GET['vnp_ResponseCode'];

if ($responseCode === "00") {
    // Thanh toán thành công
    $stmt = $conn->prepare("UPDATE donhang SET trangThai = 1 WHERE maDonHang = ?");
    $stmt->bind_param("i", $maDonHang);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['cart']);

    echo "<script>
        alert('🎉 Thanh toán VNPay thành công!');
        window.location='order_success.php';
    </script>";
    exit;
} else {
    // Thanh toán thất bại
    $stmt = $conn->prepare("UPDATE donhang SET trangThai = 2 WHERE maDonHang = ?");
    $stmt->bind_param("i", $maDonHang);
    $stmt->execute();
    $stmt->close();

    echo "<script>
        alert('❌ Thanh toán thất bại!');
        window.location='cart.php';
    </script>";
    exit;
}
