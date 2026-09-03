<?php
session_start();
include "db.php";

/* Lấy id shop từ query string */
$shopId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($shopId <= 0) {
    echo "Shop không hợp lệ";
    exit;
}

/* Lấy thông tin shop (người bán) */
$s = $conn->prepare("SELECT maTaiKhoan, tenNguoiDung, tenDangNhap, soDienThoai, avatar FROM taikhoan WHERE maTaiKhoan = ?");
$s->bind_param("i", $shopId);
$s->execute();
$shop = $s->get_result()->fetch_assoc();
$s->close();

if (!$shop) {
    echo "Không tìm thấy shop";
    exit;
}

/* Tìm avatar hiển thị (ưu tiên DB.avatar, sau đó các file theo quy ước, cuối cùng placeholder) */
$sellerAvatar = !empty($shop['avatar']) ? $shop['avatar'] : null;
$exts = ['png','jpg','jpeg','webp','gif'];

if (empty($sellerAvatar)) {
    foreach ($exts as $e) {
        $cand = "assets/img/avatars/user_{$shopId}." . $e;
        if (is_file(__DIR__ . '/' . $cand)) {
            $sellerAvatar = $cand;
            break;
        }
    }
}

if (empty($sellerAvatar)) {
    foreach ($exts as $e) {
        $cand = "assets/img/shops/{$shopId}." . $e;
        if (is_file(__DIR__ . '/' . $cand)) {
            $sellerAvatar = $cand;
            break;
        }
    }
}

if (empty($sellerAvatar) || !is_file(__DIR__ . '/' . $sellerAvatar)) {
    $sellerAvatar = "assets/img/shop-placeholder.png";
}

$cacheBuster = is_file(__DIR__ . '/' . $sellerAvatar) ? '?v=' . filemtime(__DIR__ . '/' . $sellerAvatar) : '';

/* PAGINATION */
$limit = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

/* Tổng sản phẩm của shop */
$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM sanpham WHERE maNguoiBan = ? AND trangThai = 1 AND duyetTrangThai = 1");
$total_stmt->bind_param("i", $shopId);
$total_stmt->execute();
$total_row = $total_stmt->get_result()->fetch_assoc();
$totalItems = intval($total_row['total'] ?? 0);
$totalPages = max(1, ceil($totalItems / $limit));
$total_stmt->close();

/* Lấy sản phẩm của shop (có trung bình sao) */
$sql = "
SELECT sp.maSanPham,
       sp.tenSanPham,
       sp.moTa,
       sp.gia,
       sp.giamGia,
       sp.hinhAnh,
       sp.soLuong,
       IFNULL(AVG(dg.soSao),0) AS saoTB
FROM sanpham sp
LEFT JOIN danhgia dg ON sp.maSanPham = dg.maSanPham
WHERE sp.maNguoiBan = ? AND sp.trangThai = 1 AND sp.duyetTrangThai = 1
GROUP BY sp.maSanPham
ORDER BY sp.maSanPham DESC
LIMIT {$limit} OFFSET {$offset}
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shopId);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shop: <?= htmlspecialchars($shop['tenNguoiDung'] ?: $shop['tenDangNhap']) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .shop-header{display:flex;align-items:center;gap:16px}
    .shop-avatar{width:96px;height:96px;border-radius:50%;object-fit:cover;border:2px solid #eee}
    .shop-meta h2{margin:0;font-size:22px}
    .shop-meta .phone{color:#666;margin-top:6px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:18px}
    .card{background:#fff;padding:12px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.06)}

    /* Báo cáo: viền xanh, nền trong suốt, chữ xanh */
    .btn-report{
      background:transparent;
      color:#2f903f;         
      border:1.5px solid #2f903f; 
      padding:8px 14px;
      border-radius:10px;
      text-decoration:none;
      font-weight:700;
      transition: background .12s ease, color .12s ease, transform .08s;
      display:inline-flex;
      align-items:center;
      gap:8px;
    }
    .btn-report:hover{
      background:rgba(47,144,63,0.06);
      transform:translateY(-2px);
    }
    .btn-report:active{ transform:translateY(0); }
  </style>
</head>
<body>

<header class="topbar">
  <div class="container">
    <div class="logo-title">
      <img src="assets/img/LOGO.png" alt="Logo" class="logo">
      <h1><a href="index.php">Shop Đồ Cũ</a></h1>
    </div>
    <div class="nav">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span>Xin chào, <?= htmlspecialchars($_SESSION['tenNguoiDung'] ?? '') ?></span>
        <a href="admin/logout.php">Đăng xuất</a>
      <?php else: ?>
        <a href="admin/login.php">Đăng nhập</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container">
  <div class="shop-top" style="display:flex;flex-direction:column;gap:24px;margin:20px 0;">
    
    <!-- Phần thông tin Shop ở trên -->
    <div class="shop-header-card" style="background:#fff;padding:20px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
      <div class="shop-header">
        <img src="<?= htmlspecialchars($sellerAvatar . $cacheBuster) ?>" alt="Avatar" class="shop-avatar">
        <div class="shop-meta">
          <h2><?= htmlspecialchars($shop['tenNguoiDung'] ?: $shop['tenDangNhap']) ?></h2>
          <?php if (!empty($shop['soDienThoai'])): ?>
            <div class="phone">📞 <?= htmlspecialchars($shop['soDienThoai']) ?></div>
          <?php endif; ?>
          <div style="margin-top:6px;">
            <a href="mailto:">Liên hệ người bán</a>
          </div>
        </div>
      </div>
      <div>
        <a class="btn-report" href="caidat/baocaoshop.php?seller=<?= intval($shopId) ?>">Báo cáo</a>
      </div>
    </div>

    <!-- Phần sản phẩm của shop ở dưới -->
    <div class="shop-products">
      <h3 style="margin-bottom:16px;">Sản phẩm của shop (<?= $totalItems ?>)</h3>

      <?php if ($res && $res->num_rows > 0): ?>
        <div class="grid">
          <?php while ($row = $res->fetch_assoc()): 
            $img = 'assets/img/' . ($row['hinhAnh'] ?: 'placeholder.png');
            $isHetHang = ($row['soLuong'] <= 0);
            $giam = intval($row['giamGia']);
            $giaGoc = $row['gia'];
            $giaMoi = $giaGoc * (100 - $giam) / 100;
          ?>
            <div class="card <?= $isHetHang ? 'het-hang' : '' ?>">
              <a class="card-link" href="product.php?id=<?= $row['maSanPham'] ?>">
                <div class="img-wrap" style="position:relative">
                  <?php if ($giam > 0): ?><div class="sale-badge">-<?= $giam ?>%</div><?php endif; ?>
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($row['tenSanPham']) ?>" style="width:100%;height:160px;object-fit:cover;border-radius:6px">
                  <?php if ($isHetHang): ?><div class="overlay-out">HẾT HÀNG</div><?php endif; ?>
                </div>
                <div class="title" style="margin-top:8px;font-weight:600"><?= htmlspecialchars($row['tenSanPham']) ?></div>
              </a>

              <div class="price" style="margin-top:6px">
                <?php if ($giam > 0): ?>
                  <span class="old-price" style="text-decoration:line-through;color:#888"><?= number_format($giaGoc,0,',','.') ?> ₫</span>
                  <span class="new-price" style="color:#e53935;margin-left:8px"><?= number_format($giaMoi,0,',','.') ?> ₫</span>
                <?php else: ?>
                  <?= number_format($giaGoc,0,',','.') ?> ₫
                <?php endif; ?>
              </div>

              <div class="rating" style="margin-top:6px;color:#ff9900">⭐ <?= round($row['saoTB'],1) ?></div>
              <p class="desc" style="color:#666"><?= mb_strimwidth($row['moTa'] ?? '', 0, 80, '...') ?></p>

              <div class="card-actions" style="margin-top:8px">
                <?php if ($isHetHang): ?>
                  <span class="btn-disabled">Hết hàng</span>
                <?php else: ?>
                  <button type="button" onclick="addToCart(<?= $row['maSanPham'] ?>)">🛒</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        </div>

        <!-- pagination -->
        <?php if ($totalPages > 1): ?>
          <div style="margin-top:18px;text-align:center">
            <?php if ($page > 1): ?><a href="shop.php?id=<?= $shopId ?>&page=<?= $page-1 ?>">&lt;</a><?php endif; ?>
            <?php for ($i=1;$i<=$totalPages;$i++): ?>
              <a href="shop.php?id=<?= $shopId ?>&page=<?= $i ?>" style="padding:6px 8px;margin:0 4px;<?= $i==$page ? 'background:#333;color:#fff;border-radius:4px' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?><a href="shop.php?id=<?= $shopId ?>&page=<?= $page+1 ?>">&gt;</a><?php endif; ?>
          </div>
        <?php endif; ?>

      <?php else: ?>
        <p>Shop chưa có sản phẩm.</p>
      <?php endif; ?>

    </div> <!-- .shop-products -->

  </div> <!-- .shop-top -->
</main>

<footer class="footer" style="margin-top:30px;padding:20px 0;background:#f7f7f7">
  <div class="container">
    <p>© <?= date("Y") ?> Shop Đồ Cũ</p>
  </div>
</footer>

<script>
function addToCart(id){
  fetch("cart.php?action=add&id="+id+"&qty=1")
    .then(r=>r.text())
    .then(t=>{
      if (t.trim()==="OK") alert("Đã thêm vào giỏ hàng");
      else alert("Lỗi thêm giỏ hàng");
    }).catch(()=>alert("Lỗi kết nối"));
}
</script>

</body>
</html>