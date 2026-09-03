<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập");
}

$maNguoiDung = $_SESSION['user_id'];
$vaiTro      = $_SESSION['vaitro'];

/*LẤY DANH SÁCH HÓA ĐƠN*/

if ($vaiTro == "buyer") {

    // Người mua chỉ xem hóa đơn của mình
    $stmt = $conn->prepare("
        SELECT * FROM donhang_shop
        WHERE maNguoiMua = ?
        ORDER BY ngayTao DESC
    ");

    $stmt->bind_param("i", $maNguoiDung);

} else {

    // Người bán xem TẤT CẢ hóa đơn
    $stmt = $conn->prepare("
        SELECT * FROM donhang_shop
        ORDER BY ngayTao DESC
    ");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Danh sách hóa đơn</title>
    <link rel="stylesheet" href="assets/css/hoadon.css">
</head>
<body>

<h2>DANH SÁCH HÓA ĐƠN</h2>

<?php if ($result->num_rows == 0): ?>
    <p>Chưa có hóa đơn nào.</p>
<?php else: ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Mã HĐ</th>
        <th>Mã Đơn</th>
        <th>Mã Người Mua</th>
        <th>Tổng Tiền</th>
        <th>Ngày Tạo</th>
        <th>Chi tiết</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['maDonHang_Shop'] ?></td>
        <td><?= $row['maDonHang'] ?></td>
        <td><?= $row['maNguoiMua'] ?></td>
        <td><?= number_format($row['tongTien']) ?> đ</td>
        <td><?= $row['ngayTao'] ?></td>
        <td>
            <a href="hoadon_detail.php?id=<?= $row['maDonHang_Shop'] ?>">
                Xem
            </a>
        </td>
    </tr>

    <?php endwhile; ?>

</table>

<?php endif; ?><br>
<a class="back-btn" href="cart.php">← Quay lại</a>
</body>
</html>