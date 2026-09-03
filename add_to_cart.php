<?php
session_start();
include "db.php";

$id = intval($_POST['id']);
$soLuong = intval($_POST['soluong'] ?? 1);

$sql = "
SELECT sp.tenSanPham, sp.gia, sp.maNguoiBan
FROM sanpham sp
WHERE sp.maSanPham = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$sp = $stmt->get_result()->fetch_assoc();

if (!$sp) {
    die("Sản phẩm không tồn tại");
}

$_SESSION['cart'][$id] = [
    'id' => $id,
    'ten' => $sp['tenSanPham'],
    'gia' => $sp['gia'],
    'soLuong' => $soLuong,
    'maNguoiBan' => $sp['maNguoiBan']
];

header("Location: cart.php");
exit;
