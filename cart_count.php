<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT SUM(soLuong) as total 
    FROM giohang 
    WHERE maNguoiDung = ?
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

echo $res['total'] ?? 0;