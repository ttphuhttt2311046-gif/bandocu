<?php
session_start();
include "db.php";

/* CHECK ĐĂNG NHẬP */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/*  LẤY ĐƠN HÀNG CỦA KHÁCH */
$sql = "
SELECT 
    dh.id AS donhang_id,
    dh.tongTien,
    dh.trangThai AS trangThaiDon,
    dh.ngayTao,
    tt.phuongThuc,
    tt.trangThai AS trangThaiThanhToan
FROM donhang dh
LEFT JOIN thanhtoan tt ON dh.id = tt.donhang_id
WHERE dh.user_id = ?
ORDER BY dh.ngayTao DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch sử mua hàng</title>
<style>
body{
    font-family: Arial, sans-serif;
    background:#f5f6fa;
}
.container{
    width:90%;
    max-width:1100px;
    margin:40px auto;
}
h2{
    text-align:center;
    margin-bottom:30px;
}
.order{
    background:#fff;
    border-radius:8px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}
.order-header{
    display:flex;
    justify-content:space-between;
    margin-bottom:10px;
}
.badge{
    padding:5px 10px;
    border-radius:5px;
    font-size:13px;
    color:#fff;
}
.paid{ background:#2ecc71; }
.unpaid{ background:#e67e22; }
.processing{ background:#3498db; }
.shipping{ background:#9b59b6; }
.done{ background:#27ae60; }

.products{
    margin-top:15px;
}
.product{
    display:flex;
    align-items:center;
    margin-bottom:10px;
}
.product img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:5px;
    margin-right:10px;
}
.total{
    text-align:right;
    font-weight:bold;
    margin-top:10px;
}
</style>
</head>

<body>
<div class="container">
<h2>🧾 Lịch sử mua hàng</h2>

<?php while($row = $orders->fetch_assoc()): ?>

<?php
/* TRẠNG THÁI THANH TOÁN */
$paymentText = "Chưa thanh toán";
$paymentClass = "unpaid";

if ($row['phuongThuc'] === 'vnpay' && $row['trangThaiThanhToan'] === 'success') {
    $paymentText = "Đã thanh toán (VNPay)";
    $paymentClass = "paid";
} elseif ($row['phuongThuc'] === 'cod') {
    $paymentText = "Thanh toán khi nhận hàng (COD)";
    $paymentClass = "unpaid";
}

/*TRẠNG THÁI ĐƠN*/
switch ($row['trangThaiDon']) {
    case 0:
        $orderText = "Đang xử lý";
        $orderClass = "processing";
        break;
    case 1:
        $orderText = "Đang giao";
        $orderClass = "shipping";
        break;
    case 2:
        $orderText = "Hoàn thành";
        $orderClass = "done";
        break;
    default:
        $orderText = "Không xác định";
        $orderClass = "processing";
}
?>

<div class="order">
    <div class="order-header">
        <div>
            <strong>Đơn #<?= $row['donhang_id'] ?></strong><br>
            <small><?= date("d/m/Y H:i", strtotime($row['ngayTao'])) ?></small>
        </div>
        <div>
            <span class="badge <?= $paymentClass ?>"><?= $paymentText ?></span>
            <span class="badge <?= $orderClass ?>"><?= $orderText ?></span>
        </div>
    </div>

    <!-- DANH SÁCH SẢN PHẨM -->
    <div class="products">
        <?php
        $sql_items = "
        SELECT sp.tenSP, sp.hinhAnh, ct.soLuong, ct.donGia
        FROM chitietdonhang ct
        JOIN sanpham sp ON ct.sanpham_id = sp.id
        WHERE ct.donhang_id = ?
        ";
        $stmt2 = $conn->prepare($sql_items);
        $stmt2->bind_param("i", $row['donhang_id']);
        $stmt2->execute();
        $items = $stmt2->get_result();

        while($item = $items->fetch_assoc()):
        ?>
        <div class="product">
            <img src="uploads/<?= $item['hinhAnh'] ?>">
            <div>
                <strong><?= $item['tenSP'] ?></strong><br>
                SL: <?= $item['soLuong'] ?> × <?= number_format($item['donGia']) ?>đ
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="total">
        Tổng tiền: <?= number_format($row['tongTien']) ?>đ
    </div>
</div>

<?php endwhile; ?>

</div>
</body>
</html>
