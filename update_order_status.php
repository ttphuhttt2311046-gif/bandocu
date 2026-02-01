<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['vaitro'] !== 'seller') {
    header("Location: admin/login.php");
    exit;
}

$maNguoiBan = intval($_SESSION['user_id']);
$maDonHang  = intval($_GET['id']);
$status     = $_GET['status'] ?? '';

$sql = "
UPDATE donhang 
SET trangThai = ?
WHERE maDonHang = ?
AND maDonHang IN (
    SELECT maDonHang FROM chitietdonhang WHERE maNguoiBan = ?
)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $status, $maDonHang, $maNguoiBan);
$stmt->execute();

echo "<script>
alert('Đã cập nhật trạng thái đơn hàng!');
window.location='seller_orders.php';
</script>";
