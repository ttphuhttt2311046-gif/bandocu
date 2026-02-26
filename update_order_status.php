<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['vaitro'] !== 'seller') {
    die("Không có quyền");
}

$maDonHang = intval($_GET['id']);
$status    = intval($_GET['status']); // 0-3

// CHỈ CHO PHÉP 0-3
if (!in_array($status, [0,1,2,3])) {
    die("Trạng thái không hợp lệ");
}

$stmt = $conn->prepare("
    UPDATE donhang 
    SET trangThai = ?
    WHERE maDonHang = ?
");
$stmt->bind_param("ii", $status, $maDonHang);
$stmt->execute();

header("Location: seller_orders.php");
exit;
