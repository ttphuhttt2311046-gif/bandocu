<?php
session_start();
include "db.php";

/* ===== LẤY ID ===== */
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* ===== LẤY SẢN PHẨM ===== */
$stmt = $conn->prepare("SELECT * FROM sanpham WHERE maSanPham = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
$stmt->close();

/* ===== NGƯỜI BÁN ===== */
$sellerName = '';
if ($product && !empty($product['maNguoiBan'])) {
    $stmtSeller = $conn->prepare(
        "SELECT tenNguoiDung, tenDangNhap FROM taikhoan WHERE maTaiKhoan=?"
    );
    $stmtSeller->bind_param("i", $product['maNguoiBan']);
    $stmtSeller->execute();
    $seller = $stmtSeller->get_result()->fetch_assoc();
    if ($seller) {
        $sellerName = $seller['tenNguoiDung'] ?: $seller['tenDangNhap'];
    }
    $stmtSeller->close();
}
/* ===== TÍNH GIẢM GIÁ ===== */
$giamApDung = 0;
$giaMoi = $product['gia'] ?? 0;
if ($product) {
    $giaGoc = $product['gia'];
    $giamCaNhan = intval($product['giamGia']);
    $giamSuKien = 0;
    
    $now = date('Y-m-d H:i:s');
    $eventSql = "SELECT phanTramGiam FROM sukien_giamgia 
                 WHERE trangThai = 1 AND batDau <= '$now' AND ketThuc >= '$now' LIMIT 1";
    $eventRes = $conn->query($eventSql);
    if ($eventRes && $eventRes->num_rows > 0) {
        $event = $eventRes->fetch_assoc();
        if ($product['tuChoiSuKien'] == 0) {
            $giamSuKien = intval($event['phanTramGiam']);
        }
    }
    
    $giamApDung = max($giamCaNhan, $giamSuKien);
    $giaMoi = $giaGoc * (100 - $giamApDung) / 100;
}
/* ===== CẬP NHẬT TÌNH TRẠNG ===== */
if ($product) {
    $newStatus = ($product['soLuong'] > 0) ? 'Còn hàng' : 'Hết hàng';
    if ($product['tinhTrang'] !== $newStatus) {
        $u = $conn->prepare("UPDATE sanpham SET tinhTrang=? WHERE maSanPham=?");
        $u->bind_param("si", $newStatus, $id);
        $u->execute();
        $u->close();
        $product['tinhTrang'] = $newStatus;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Chi tiết sản phẩm</title>
<link rel="stylesheet" href="assets/css/stylechitietsp.css">
</head>

<body>

<header class="topbar">
  <div class="container topbar-inner">
    <h1><a href="index.php">Shop Đồ Cũ</a></h1>
    <nav>
      <a href="cart.php">🛒Giỏ hàng</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <span>Xin chào, <?php echo htmlspecialchars($_SESSION['tenNguoiDung']); ?></span>
        <a href="admin/logout.php">Đăng xuất</a>
      <?php else: ?>
        <a href="admin/login.php">Đăng nhập</a>
        <a href="admin/register.php">Đăng ký</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
<?php if ($product): ?>

<section class="product-box">

  <!-- ===== CỘT TRÁI ===== -->
  <div class="gallery">
    <img class="main-img1"
         src="assets/img/<?php echo htmlspecialchars($product['hinhAnh']); ?>">

    <div class="thumbs1">
      <?php for ($i=1;$i<=3;$i++):
        $f='hinhAnh'.$i;
        if(!empty($product[$f])): ?>
          <img src="assets/img_phu/<?php echo htmlspecialchars($product[$f]); ?>">
      <?php endif; endfor; ?>
    </div>

    <!-- TÊN + NÚT DƯỚI ẢNH -->
    <div class="left-action">
      <h3><?php echo htmlspecialchars($product['tenSanPham']); ?></h3>

      <form action="cart.php" method="get">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="id" value="<?php echo $product['maSanPham']; ?>">
        <input type="number" name="qty" value="1" min="1">
        <button type="submit">Thêm vào giỏ</button>
      </form>
    </div>
  </div>

  <!-- ===== CỘT PHẢI ===== -->
  <div class="info-box">

    <div class="price">
      <?php if ($giamApDung > 0): ?>
        <span style="text-decoration: line-through; color: #999;">
          <?php echo number_format($product['gia'],0,',','.'); ?> ₫
        </span>
        <br>
        <span style="color: red; font-size: 24px; font-weight: bold;">
          <?php echo number_format($giaMoi,0,',','.'); ?> ₫
        </span>
        <span style="background: red; color: white; padding: 2px 6px; border-radius: 4px; margin-left: 10px; font-size: 18px;">
          -<?php echo $giamApDung; ?>%
        </span>
      <?php else: ?>
        <?php echo number_format($product['gia'],0,',','.'); ?> ₫
      <?php endif; ?>
    </div>

    <div class="meta">
      <span class="<?php echo $product['soLuong']>0?'ok':'no'; ?>">
        <?php echo $product['tinhTrang']; ?>
      </span>
      <span>Còn <?php echo $product['soLuong']; ?> sản phẩm</span>
    </div>

    <div class="desc">
      <?php echo nl2br(htmlspecialchars($product['moTa'])); ?>
    </div>

    <?php if ($sellerName): ?>
      <div class="seller">👤 Người bán: <?php echo htmlspecialchars($sellerName); ?></div>
    <?php endif; ?>

  </div>

</section>

<?php else: ?>
<p>Không tìm thấy sản phẩm.</p>
<?php endif; ?>
</main>

<footer class="footer">
  © <?php echo date("Y"); ?> Shop Đồ Cũ
</footer>

<script>
const main=document.querySelector('.main-img1');
document.querySelectorAll('.thumbs1 img').forEach(img=>{
  img.onclick=()=>{ [main.src,img.src]=[img.src,main.src]; }
});
</script>

</body>
</html>
