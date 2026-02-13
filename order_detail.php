<?php
session_start();
include "db.php";

if (!isset($_GET['id'])) {
    die("Không tìm thấy đơn hàng");
}

$maDonHang = intval($_GET['id']);

/* =============================
   LẤY ĐƠN HÀNG
============================= */
$sql = "SELECT * FROM donhang WHERE maDonHang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maDonHang);
$stmt->execute();
$result = $stmt->get_result();
$donhang = $result->fetch_assoc();

if (!$donhang) {
    die("Đơn hàng không tồn tại");
}

/* =============================
   HÀM HIỂN THỊ TRẠNG THÁI
============================= */
function hienThiTrangThai($status){
    switch((string)$status){
        case "0": return "Chờ xác nhận";
        case "1": return "Đang giao";
        case "2": return "Hoàn thành";
        case "3": return "Đã hủy";
        case "cho_xac_nhan": return "Chờ xác nhận";
        case "dang_giao": return "Đang giao";
        case "hoan_thanh": return "Hoàn thành";
        case "da_huy": return "Đã hủy";
    }
    return "Chờ xác nhận";
}

/* =============================
   KIỂM TRA ĐƯỢC HỦY
============================= */
function duocHuy($status){
    return ($status == 0 || $status === "0" || $status == "cho_xac_nhan");
}

/* =============================
   XỬ LÝ HỦY ĐƠN
============================= */
if (isset($_POST['huydon']) && duocHuy($donhang['trangThai'])) {

    if (is_numeric($donhang['trangThai'])) {
        $update = $conn->prepare("UPDATE donhang SET trangThai = 3 WHERE maDonHang = ?");
    } else {
        $update = $conn->prepare("UPDATE donhang SET trangThai = 'da_huy' WHERE maDonHang = ?");
    }

    $update->bind_param("i", $maDonHang);
    $update->execute();

    header("Location: order_detail.php?id=" . $maDonHang);
    exit();
}

/* =============================
   LẤY CHI TIẾT ĐƠN
============================= */
$sql_ct = "
SELECT ct.*, sp.tenSanPham, sp.hinhAnh
FROM chitietdonhang ct
JOIN sanpham sp ON ct.maSanPham = sp.maSanPham
WHERE ct.maDonHang = ?
";

$stmt_ct = $conn->prepare($sql_ct);
$stmt_ct->bind_param("i", $maDonHang);
$stmt_ct->execute();
$result_ct = $stmt_ct->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết đơn hàng</title>
<link rel="stylesheet" href="assets/css/order_detail.css">
</head>
<body>

<div class="container">

<h2>Chi tiết đơn hàng #<?= $maDonHang ?></h2>

<div class="info">
    <p><b>Mã đơn:</b> <?= $donhang['maDonHang'] ?></p>
    <p><b>Ngày đặt:</b> <?= $donhang['ngayDat'] ?></p>
    <p><b>Tổng tiền:</b> <?= number_format($donhang['tongTien']) ?> đ</p>
    <p><b>Trạng thái:</b> 
        <span class="status">
            <?= hienThiTrangThai($donhang['trangThai']) ?>
        </span>
    </p>
</div>

<?php if (duocHuy($donhang['trangThai'])): ?>
<form method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn?');">
    <button type="submit" name="huydon" class="cancel-btn">
        ❌ Hủy đơn
    </button>
</form>
<?php endif; ?>

<table>
<tr>
    <th>Hình ảnh</th>
    <th>Tên sản phẩm</th>
    <th>Đơn giá</th>
    <th>Số lượng</th>
    <th>Thành tiền</th>
</tr>

<?php while($row = $result_ct->fetch_assoc()): ?>
<tr>
    <td>
        <?php
        $imgPath = "assets/img/" . $row['hinhAnh'];
        if (!empty($row['hinhAnh']) && file_exists($imgPath)):
        ?>
            <img src="<?= $imgPath ?>" 
                 onerror="this.src='assets/css/no-image.png'">
        <?php else: ?>
            <img src="assets/css/no-image.png">
        <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($row['tenSanPham']) ?></td>
    <td><?= number_format($row['donGia']) ?> đ</td>
    <td><?= $row['soLuong'] ?></td>
    <td><?= number_format($row['thanhTien']) ?> đ</td>
</tr>
<?php endwhile; ?>

</table>

<a href="orders.php" class="back-btn">← Quay lại</a>

</div>

</body>
</html>
