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

$maTaiKhoan = intval($_SESSION['user_id']);

/* ======================
   LẤY LỊCH SỬ THANH TOÁN
====================== */
$sql = $conn->prepare("
    SELECT 
        dh.maDonHang,
        dh.ngayDat,
        dh.tongTien,
        tt.phuongThuc,
        tt.trangThai,
        tt.nganHang,
        tt.maGiaoDich
    FROM donhang dh
    LEFT JOIN thanhtoan tt ON dh.maDonHang = tt.maDonHang
    WHERE dh.maNguoiMua = ?
    ORDER BY dh.ngayDat DESC
");
$sql->bind_param("i", $maTaiKhoan);
$sql->execute();
$result = $sql->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch sử thanh toán</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f6fa;
    padding: 30px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    max-width: 1000px;
    margin: auto;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,.1);
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

th {
    background: #2ecc71;
    color: white;
}

.status-success {
    color: green;
    font-weight: bold;
}

.status-pending {
    color: orange;
    font-weight: bold;
}

.back {
    display: block;
    text-align: center;
    margin-top: 20px;
    text-decoration: none;
}
</style>
</head>

<body>

<h2>📜 Lịch sử thanh toán</h2>

<table>
<tr>
    <th>Mã đơn</th>
    <th>Ngày đặt</th>
    <th>Tổng tiền</th>
    <th>Phương thức</th>
    <th>Ngân hàng</th>
    <th>Mã giao dịch</th>
    <th>Trạng thái</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td>#<?= $row['maDonHang'] ?></td>
    <td><?= $row['ngayDat'] ?></td>
    <td><?= number_format($row['tongTien']) ?> VNĐ</td>
    <td><?= strtoupper($row['phuongThuc'] ?? '---') ?></td>
    <td><?= $row['nganHang'] ?? '---' ?></td>
    <td><?= $row['maGiaoDich'] ?? '---' ?></td>
    <td class="<?= $row['trangThai'] === 'success' ? 'status-success' : 'status-pending' ?>">
        <?= $row['trangThai'] ?? 'Chưa thanh toán' ?>
    </td>
</tr>
<?php endwhile; ?>
</table>

<a href="index.php" class="back">← Về trang chủ</a>

</body>
</html>
