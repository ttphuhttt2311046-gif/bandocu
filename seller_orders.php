<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['vaitro'] !== 'seller') {
    header("Location: admin/login.php");
    exit;
}

$maNguoiBan = intval($_SESSION['user_id']);

$sql = "
SELECT DISTINCT
    dh.maDonHang,
    dh.ngayDat,
    dh.tongTien,
    dh.trangThai,
    tk.tenNguoiDung AS tenNguoiMua,
    tk.diaChi AS diaChiNguoiMua
FROM donhang dh
JOIN chitietdonhang ct ON dh.maDonHang = ct.maDonHang
JOIN taikhoan tk ON dh.maNguoiMua = tk.maTaiKhoan
WHERE ct.maNguoiBan = ?
ORDER BY dh.maDonHang DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maNguoiBan);
$stmt->execute();
$result = $stmt->get_result();

/* ====== HÀM HIỂN THỊ TRẠNG THÁI ====== */
function hienThiTrangThai($status) {
    switch ($status) {
        case 0:
            return ['⏳ Đang xử lý', 'status-processing'];
        case 1:
            return ['🚚 Đang giao', 'status-shipping'];
        case 2:
            return ['✅ Hoàn thành', 'status-done'];
        case 3:
            return ['❌ Đã hủy', 'status-cancel'];
        default:
            return ['❓ Không xác định', 'status-unknown'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Đơn hàng của tôi</title>

<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/style1.css">

<style>
.status-processing { color:#6c757d; font-weight:600; }
.status-shipping   { color:#17a2b8; font-weight:600; }
.status-done       { color:#28a745; font-weight:600; }
.status-cancel     { color:#dc3545; font-weight:600; }
.status-unknown    { color:#333; font-weight:600; }
</style>
</head>

<body>
<div class="seller-container">
    <h2 class="seller-title">📦 Đơn hàng của tôi</h2>

    <table class="seller-table">
        <tr>
            <th>Mã đơn</th>
            <th>Người mua</th>
            <th>Địa chỉ</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>

        <?php while ($r = $result->fetch_assoc()): ?>
        <?php
            [$statusText, $statusClass] = hienThiTrangThai($r['trangThai']);
        ?>
        <tr>
            <td>#<?= $r['maDonHang'] ?></td>
            <td><?= htmlspecialchars($r['tenNguoiMua']) ?></td>
            <td><?= htmlspecialchars($r['diaChiNguoiMua']) ?></td>
            <td><?= $r['ngayDat'] ?></td>
            <td><?= number_format($r['tongTien'], 0, ',', '.') ?> VND</td>

            <td>
                <span class="<?= $statusClass ?>">
                    <?= $statusText ?>
                </span>
            </td>

            <td>
                <a href="seller_order_detail.php?id=<?= $r['maDonHang'] ?>">Xem</a>

                <?php if ($r['trangThai'] != 2 && $r['trangThai'] != 3): ?>
                    | <a href="update_order_status.php?id=<?= $r['maDonHang'] ?>&status=1">Đang giao</a>
                    | <a href="update_order_status.php?id=<?= $r['maDonHang'] ?>&status=2">Hoàn thành</a>
                    | <a href="update_order_status.php?id=<?= $r['maDonHang'] ?>&status=3">Hủy</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a class="back-link" href="index.php">⬅ Quay lại</a>
</div>
</body>
</html>
