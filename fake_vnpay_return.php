<?php
session_start();
include "db.php";

/* =========================
   KIỂM TRA DỮ LIỆU TRẢ VỀ
========================= */
if (!isset($_GET['vnp_ResponseCode']) || !isset($_GET['vnp_TxnRef'])) {
    die("Dữ liệu VNPay không hợp lệ");
}

$maDonHang   = intval($_GET['vnp_TxnRef']);
$responseCode = $_GET['vnp_ResponseCode'];
$soTien      = isset($_GET['vnp_Amount']) ? ($_GET['vnp_Amount'] / 100) : 0;
$nganHang    = $_GET['vnp_BankCode'] ?? 'VNPAY';
$maGiaoDich  = $_GET['vnp_TransactionNo'] ?? ('VNPAY' . time());

/* =========================
   KIỂM TRA KẾT QUẢ THANH TOÁN
========================= */
if ($responseCode !== '00') {
    echo "<script>
        alert('❌ Thanh toán VNPay thất bại');
        window.location='checkout.php';
    </script>";
    exit;
}

/* =========================
   CẬP NHẬT ĐƠN HÀNG
   0 = ĐANG XỬ LÝ (CHỜ SELLER DUYỆT)
========================= */
$stmt = $conn->prepare("
    UPDATE donhang 
    SET trangThai = 0
    WHERE maDonHang = ?
");
$stmt->bind_param("i", $maDonHang);
$stmt->execute();
$stmt->close();

/* =========================
   GHI LỊCH SỬ THANH TOÁN
========================= */
$stmtTT = $conn->prepare("
    INSERT INTO thanhtoan 
    (maDonHang, phuongThuc, soTien, trangThai, nganHang, maGiaoDich)
    VALUES (?, 'vnpay', ?, 'success', ?, ?)
");
$stmtTT->bind_param("idss", $maDonHang, $soTien, $nganHang, $maGiaoDich);
$stmtTT->execute();
$stmtTT->close();

/* =========================
   XÓA GIỎ HÀNG
========================= */
unset($_SESSION['cart']);
unset($_SESSION['maDonHang']);

/* =========================
   CHUYỂN TRANG THÀNH CÔNG
========================= */
header("Location: order_success.php");
exit;
