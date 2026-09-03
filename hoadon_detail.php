<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    die("Bạn chưa đăng nhập");
}

$maNguoiDung = $_SESSION['user_id'];
$vaiTro = $_SESSION['vaitro'];

if(!isset($_GET['id'])){
    die("Không tìm thấy hóa đơn");
}

$maHoaDon = intval($_GET['id']);

/* KIỂM TRA QUYỀN */

if($vaiTro == "buyer"){
    $stmt = $conn->prepare("SELECT * FROM donhang_shop 
                            WHERE maDonHang_Shop = ? 
                            AND maNguoiMua = ?");
    $stmt->bind_param("ii", $maHoaDon, $maNguoiDung);
}
else if($vaiTro == "seller"){
    $stmt = $conn->prepare("SELECT * FROM donhang_shop 
                            WHERE maDonHang_Shop = ?");
    $stmt->bind_param("i", $maHoaDon);
}
else{
    die("Vai trò không hợp lệ");
}

$stmt->execute();
$resultHD = $stmt->get_result();

if($resultHD->num_rows == 0){
    die("Bạn không có quyền xem hóa đơn này!");
}

$hoadon = $resultHD->fetch_assoc();

/* ===== LẤY CHI TIẾT ===== */

$stmtCT = $conn->prepare("
    SELECT c.soLuong, c.donGia, s.tenSanPham, s.hinhAnh
    FROM chitietdonhang c
    JOIN sanpham s ON c.maSanPham = s.maSanPham
    WHERE c.maDonHang_Shop = ?
");
$stmtCT->bind_param("i", $maHoaDon);
$stmtCT->execute();
$resultCT = $stmtCT->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chi tiết hóa đơn</title>
    <link rel="stylesheet" href="assets/css/hoadon_detail.css">
</head>
<body>

<div class="container">

<h2>CHI TIẾT HÓA ĐƠN #<?= $hoadon['maDonHang_Shop'] ?></h2>

<div class="info-box">
    <p><strong>Mã đơn hàng:</strong> <?= $hoadon['maDonHang'] ?></p>
    <p><strong>Ngày tạo:</strong> <?= $hoadon['ngayTao'] ?></p>
    <p><strong>Tổng tiền:</strong> <?= number_format($hoadon['tongTien']) ?> đ</p>
</div>

<h3>Danh sách sản phẩm</h3>

<?php if($resultCT->num_rows > 0): ?>
<table>
    <tr>
    
        <th>Tên sản phẩm</th>
        <th>Số lượng</th>
        <th>Đơn giá</th>
        <th>Thành tiền</th>
    </tr>

    <?php while($row = $resultCT->fetch_assoc()): ?>
    <tr>
        
        <td><?= $row['tenSanPham'] ?></td>
        <td><?= $row['soLuong'] ?></td>
        <td><?= number_format($row['donGia']) ?> đ</td>
        <td><?= number_format($row['soLuong'] * $row['donGia']) ?> đ</td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>Hóa đơn này chưa có sản phẩm.</p>
<?php endif; ?>

<div class="total">
    Tổng cộng: <?= number_format($hoadon['tongTien']) ?> đ
</div>

<br>
<a class="back-btn" href="hoadon.php">← Quay lại</a>

</div>

</body>
</html>