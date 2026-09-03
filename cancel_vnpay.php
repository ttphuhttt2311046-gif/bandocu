<?php
session_start();
include "db.php";

if (!isset($_GET['vnp_TxnRef'])) {
    header("Location: cart.php");
    exit;
}

$maDonHang = intval($_GET['vnp_TxnRef']);

$conn->begin_transaction();

try {

    // Xóa thanh toán
    $stmt = $conn->prepare("DELETE FROM thanhtoan WHERE maDonHang = ?");
    $stmt->bind_param("i", $maDonHang);
    $stmt->execute();
    $stmt->close();

    // Xóa chi tiết đơn
    $stmt = $conn->prepare("DELETE FROM chitietdonhang WHERE maDonHang = ?");
    $stmt->bind_param("i", $maDonHang);
    $stmt->execute();
    $stmt->close();

    // Xóa đơn
    $stmt = $conn->prepare("DELETE FROM donhang WHERE maDonHang = ?");
    $stmt->bind_param("i", $maDonHang);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
}

echo "<script>
    alert('Bạn đã hủy thanh toán');
    window.location='cart.php';
</script>";
exit;