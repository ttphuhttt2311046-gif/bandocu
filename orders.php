<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$maTaiKhoan = intval($_SESSION['user_id']);

$sql = "
SELECT 
    dh.maDonHang,
    dh.ngayDat,
    dh.tongTien,
    dh.trangThai,
    tt.phuongThuc
FROM donhang dh
LEFT JOIN thanhtoan tt ON dh.maDonHang = tt.maDonHang
WHERE dh.maNguoiMua = ?
GROUP BY dh.maDonHang
ORDER BY dh.maDonHang DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maTaiKhoan);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch sử mua hàng</title>
<link rel="stylesheet" href="assets/css/style.css">

<style>
.order-container {
    max-width: 1000px;
    margin: 40px auto;
    background: #fff;
    padding: 25px;
    border-radius: 10px;
}
.order-table {
    width: 100%;
    border-collapse: collapse;
}
.order-table th,
.order-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: center;
}
.order-table th {
    background: #f5f5f5;
}

.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
}
.badge-success { background: #28a745; color: #fff; }
.badge-warning { background: #ffc107; color: #000; }
.badge-secondary { background: #6c757d; color: #fff; }
.badge-danger { background: #dc3545; color: #fff; }
.badge-info { background: #17a2b8; color: #fff; }
</style>
</head>

<body>

<div class="order-container">
    <h2>🛒 Lịch sử mua hàng</h2>

    <table class="order-table">
        <tr>
            <th>Mã đơn</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Thanh toán</th>
            <th>Trạng thái đơn</th>
            <th>Chi tiết</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>#<?= $row['maDonHang'] ?></td>
            <td><?= $row['ngayDat'] ?></td>
            <td><?= number_format($row['tongTien'], 0, ',', '.') ?> VNĐ</td>

            <!-- THANH TOÁN -->
            <td>
                <?php
                if ($row['phuongThuc'] === 'vnpay' || $row['phuongThuc'] === 'fake_vnpay') {
                    echo '<span class="badge badge-success">Đã thanh toán</span>';
                } else {
                    echo '<span class="badge badge-warning">COD</span>';
                }
                ?>
            </td>

            <!-- TRẠNG THÁI ĐƠN -->
            <td>
                <?php
                switch ((int)$row['trangThai']) {
                    case 0:
                        echo '<span class="badge badge-secondary">⏳ Đang xử lý</span>';
                        break;
                    case 1:
                        echo '<span class="badge badge-info">🚚 Đang giao</span>';
                        break;
                    case 2:
                        echo '<span class="badge badge-success">✅ Hoàn thành</span>';
                        break;
                    case 3:
                        echo '<span class="badge badge-danger">❌ Đã hủy</span>';
                        break;
                    default:
                        echo '<span class="badge badge-secondary">❓ Không xác định</span>';
                }
                ?>
            </td>

            <td>
                <a href="order_detail.php?id=<?= $row['maDonHang'] ?>">Xem</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="index.php">⬅ Quay lại trang chủ</a>
</div>

</body>
</html>
