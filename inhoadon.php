<?php
session_start();
require_once __DIR__ . "/db.php";

$id = intval($_GET['id']);

/* ===== LẤY ĐƠN ===== */
$sql = "
SELECT 
    dh.maDonHang,
    dh.ngayDat,
    dh.tongTien,
    dh.maNguoiMua,
    tk.tenNguoiDung,
    tk.diaChi
FROM donhang dh
JOIN taikhoan tk ON dh.maNguoiMua = tk.maTaiKhoan
WHERE dh.maDonHang = $id
";

$order = mysqli_fetch_assoc(mysqli_query($conn, $sql));

/* ===== LẤY SP ===== */
$items = mysqli_query($conn, "
SELECT 
    ct.soLuong,
    ct.donGia,
    ct.thanhTien,
    sp.tenSanPham
FROM chitietdonhang ct
JOIN sanpham sp ON ct.maSanPham = sp.maSanPham
WHERE ct.maDonHang = $id
");


?>
<?php if(isset($_GET['sent'])): ?>
    <div style="
        background:#d4edda;
        color:#155724;
        padding:10px;
        margin-bottom:15px;
        border-radius:5px;
        text-align:center;
        font-weight:bold;
    ">
        ✅ Đã gửi hóa đơn PDF cho khách!
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Hóa đơn #<?= $order['maDonHang'] ?></title>

<style>
body{font-family:Segoe UI; background:#f5f5f5;}
.invoice{width:800px;margin:30px auto;background:#fff;padding:30px;border-radius:10px;}
.header{display:flex;justify-content:space-between;border-bottom:2px solid #eee;padding-bottom:10px;}
.logo{
    display:flex;
    align-items:center;
    gap:10px;
    color:#ff5722;
    font-size:22px;
    font-weight:bold;
}

.logo img{
    width:50px;
    height:50px;
    object-fit:contain;
}.info{display:flex;justify-content:space-between;margin:20px 0;}
table{width:100%;border-collapse:collapse;}
th{background:#ff5722;color:#fff;padding:10px;}
td{padding:10px;border-bottom:1px solid #eee;}
.total{text-align:right;margin-top:20px;}
button{padding:10px 20px;font-size:16px;cursor:pointer;}

@media print{
    button,#msg{display:none;}
    body{background:#fff;}
}
</style>
</head>

<body>

<div class="invoice">

<div class="header">
<div class="logo">
    <img src="assets/img/LOGO.png" alt="Logo">
    <span>Shop Đồ Cũ</span>
</div>    

<div>
        <b>HÓA ĐƠN</b><br>
        #<?= $order['maDonHang'] ?>
    </div>
</div>

<div class="info">
    <div>
        <b>Người mua:</b><br>
        <?= $order['tenNguoiDung'] ?><br>
        <?= $order['diaChi'] ?>
    </div>

    <div>
        <b>Ngày:</b><br>
<?= date("d/m/Y H:i", strtotime($order['ngayDat'])) ?>    </div>
</div>

<table>
<tr>
<th>Sản phẩm</th>
<th>SL</th>
<th>Giá</th>
<th>Tiền</th>
</tr>

<?php while($i=mysqli_fetch_assoc($items)): ?>
<tr>
<td><?= $i['tenSanPham'] ?></td>
<td><?= $i['soLuong'] ?></td>
<td><?= number_format($i['donGia'],0,',','.') ?> VND</td>
<td><?= number_format($i['thanhTien'],0,',','.') ?> VND</td>
</tr>
<?php endwhile; ?>
</table>

<div class="total">
<h2>Tổng: <?= number_format($order['tongTien'],0,',','.') ?> VND</h2>
</div>

<!-- BUTTON -->
<div style="text-align:center;margin-top:20px;">
    <button onclick="inHD()">🖨 In hóa đơn</button>
<a href="seller_orders.php" style="
    display:inline-block;
    margin-left:10px;
    padding:10px 20px;
    background:#6c757d;
    color:#fff;
    text-decoration:none;
    border-radius:5px;
">
    ⬅ Quay lại
</a>
    <a href="export_pdf.php?id=<?= $id ?>" style="margin-left:10px;">
        📄 Gửi PDF cho khách
    </a>

    <p id="msg" style="color:green;"></p>
</div>

</div>

<script>
function inHD(){
    window.print();
    setTimeout(()=>{
        document.getElementById("msg").innerText="✅ In hóa đơn thành công";
    },1000);
}
</script>

</body>
</html>